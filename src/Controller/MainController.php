<?php


namespace App\Controller;

use App\Entity\Category;
use App\Entity\Discounts;
use App\Entity\SellingItem;
use App\Service\LanguageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/")
     */
    public function indexNoLocale(): Response
    {
        return $this->redirectToRoute('main', ['_locale' => 'pl']);
    }

    /**
     * @Route("/{_locale}", name="main")
     * @return Response
     */
    public function index($_locale, LanguageService $LanguageService): Response
    {
        $em = $this->getDoctrine()->getManager();
        $lang = $LanguageService->getLang($em, $_locale);
        $categoryData = $em->getRepository(Category::class)->findBy(['language' => $lang, 'publication' => 1]);

        return $this->render('main/index.html.twig', [
            'categoryData' => $categoryData
        ]);
    }

    /**
     * @Route("/{_locale}/new_products", name="new_products")
     * @return Response
     */
    public function newProducts($_locale, LanguageService $LanguageService): Response
    {
        $em = $this->getDoctrine()->getManager();
        $lang = $LanguageService->getLang($em, $_locale);
        $sellingItemData = $em->getRepository(SellingItem::class)->findBy(['language' => $lang, 'publication' => true], ['created_at' => 'DESC']);
        $categoryData = $em->getRepository(Category::class)->findBy(['language' => $lang]);

        return $this->render('main/new_products.html.twig', [
            'sellingItemData' => $sellingItemData,
            'categoryData' => $categoryData
        ]);
    }

    /**
     * @Route("/{_locale}/discounts_products", name="discounts_products")
     * @return Response
     */
    public function discountsProducts($_locale, LanguageService $LanguageService): Response
    {
        $em = $this->getDoctrine()->getManager();
        $lang = $LanguageService->getLang($em, $_locale);
        $sellingItemData = $em->getRepository(SellingItem::class)->findAllDiscounts();
        $categoryData = $em->getRepository(Category::class)->findBy(['language' => $lang]);

        return $this->render('main/discounts_products.html.twig', [
            'sellingItemData' => $sellingItemData,
            'categoryData' => $categoryData
        ]);
    }

    /**
     * @Route("/{_locale}/category_products/{id}", name="category_products")
     * @return Response
     */
    public function cateogryProducts($_locale, $id, LanguageService $LanguageService): Response
    {
        $em = $this->getDoctrine()->getManager();
        $lang = $LanguageService->getLang($em, $_locale);
        $sellingItemData = $em->getRepository(SellingItem::class)->findBy(['language' => $lang, 'category' => $id, 'publication' => true], ['created_at' => 'DESC']);
        $categoryInfo = $em->getRepository(Category::class)->findOneBy(['id' => $id]);
        $categoryData = $em->getRepository(Category::class)->findBy(['language' => $lang]);

        return $this->render('main/category_products.html.twig', [
            'sellingItemData' => $sellingItemData,
            'categoryData' => $categoryData,
            'categoryInfo' => $categoryInfo
        ]);
    }

    /**
     * @Route("/{_locale}/basket_summary/", name="basket_summary")
     */
    public function basketSummary()
    {
        $id = [];
        $new_order_summary = [];
        $end_order_summary = [];
        $basket_summary = $this->session->get('basket');

        if ($basket_summary) {
            foreach ($basket_summary as $basket) {
                array_push($id, $basket['id_product']);
            }
            $unique_id = array_unique($id);
            foreach ($unique_id as $value) {
                array_push($new_order_summary, ['id' => $value, 'quantity' => 1]);
            }
            foreach ($new_order_summary as $order) {
                $i = 0;
                foreach ($basket_summary as $basket_1) {
                    if ($order['id'] == $basket_1['id_product']) {
                        $i++;
                    }
                }
                array_push($end_order_summary, ['id' => $order['id'], 'quantity' => $i]);
            }
        }

        $em = $this->getDoctrine()->getManager();
        if (isset($unique_id)) {
            $unique_id_1 = implode(',', $unique_id);
        } else {
            $unique_id_1 = 1;
        }
        $sellingItemOrders = $em->getRepository(SellingItem::class)->findAllItemsId($unique_id_1);

        $sum_order = 0;
        foreach ($sellingItemOrders as $item) {
            foreach ($end_order_summary as $order) {
                if ($order['id'] == $item->getId()) {
                    var_dump($order['id']);
                    echo "<br>";
                    $discount = $em->getRepository(Discounts::class)->findBy(['id' => $item->getDiscounts()]);
                    foreach ($discount as  $d) {
                        $discountPercent = $d->getPercent();
                    }

                    echo "<br>";
                    var_dump($item->getPrice());
                    if (isset($discountPercent)) {
                        echo "<br>";
                        echo "Rabat jeśli istnieje: " . $discountPercent . "%";
                        echo "<br>";
                        $priceAfterDiscount = round($item->getPrice() - ($item->getPrice() / 100) * $discountPercent, 2);

                        echo "Kwota po rabacie:" . $priceAfterDiscount;
                        echo "<br>";
                        echo "suma dla tego produktu: o ID " . $item->getId() . " = " . $order['quantity'] * $priceAfterDiscount  . " poniewaz (order['quantity']) = " . $order['quantity'] . " * ( item->getPrice() )= " . $priceAfterDiscount . " (kwota po rabacie ! )";
                        $sum_order += $order['quantity'] * $priceAfterDiscount;
                    } else {
                        echo "<br>";
                        echo "suma dla tego produktu: o ID " . $item->getId() . " = " . $order['quantity'] * $item->getPrice() . " poniewaz (order['quantity']) = " . $order['quantity'] . " * ( item->getPrice() )= " . $item->getPrice();
                        $sum_order += $order['quantity'] * $item->getPrice();
                    }
                    echo "<br><br> KONIEC <br><br>";
                }
                unset($discountPercent);
            }
        }
        echo "<br><br> KONIEC podsumowanie : " . $sum_order . " PLN <br><br>";



        return $this->render('main/basket_summary.html.twig', [
            'sellingItemOrders' => $sellingItemOrders,
            'end_order_summary' =>  $end_order_summary
        ]);
    }

    /**
     * @Route("/{_locale}/add_to_basket/{id_product}", name="add_to_basket")
     */
    public function addToBasket($id_product)
    {
        $basket = $this->session->get('basket');
        if ($basket == null) {
            $basket[] = ['id_product' => $id_product, 'args' => ['quantity' => 1]];
        } else {
            $basket_add_product = ['id_product' => $id_product, 'args' => ['quantity' => 1]];
            array_push($basket, $basket_add_product);
        }

        $this->session->set('basket', $basket);
        return $this->redirectToRoute('new_products');
    }

    /**
     * @Route("/{_locale}/clear_basket/", name="clear_basket")
     */
    public function clearBasker()
    {
        $this->session->remove('basket');
        return $this->redirectToRoute('basket_summary');
    }
}
