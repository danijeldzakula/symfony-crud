<?php
    namespace App\Controller;

    use App\Entity\Product;
    use App\Form\ProductType;
    use App\Repository\ProductRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;

    #[Route('/product', name: 'product-')]
    class ProductController extends AbstractController
    {
        /* all products */
        #[Route('/all', name: 'show_all_products')]
        public function all_products(ProductRepository $productRepository): Response
        {
            return $this->render('product/index.html.twig', [
                'products' => $productRepository->findAll(),
            ]);
        }

        /* add prduct */
        #[Route('/add', name: 'add_product')]
        public function add_product(Request $request, ProductRepository $productRepository): Response 
        {
            $product = new Product();
            $form = $this->createForm(ProductType::class, $product);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $productRepository->add($product);
                return $this->redirectToRoute('product-show_all_products', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('product/add_product.html.twig', [
                'product' => $product,
                'form' => $form,
            ]);
        }

        /* sort */
        #[Route('/sort', name: 'sort')]
        public function sort(Request $request, ProductRepository $productRepository) 
        {
            $sort_by = $request->query->get('filter');
            $products = $productRepository->sort($sort_by);

            return $this->json($products);
        }

        /* test new route */
        #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
        public function new_add(Request $request): Response
        {
            $submit = $request->request->get('submit');
            $name = $request->request->get('name');

            return $this->renderForm('product/new.html.twig', [
                'name' => $name,
            ]);

            if (!isset($submit)) {
                return $this->redirectToRoute('product-show_all_products', [], Response::HTTP_SEE_OTHER);
            }
        }

        /* show product by ID */
        #[route('/{slug}', name: 'show_product', methods: ['GET'])]
        public function show_product(Product $product): Response 
        {
            return $this->render('product/show_product.html.twig', ['product' => $product ]); 
        }

        /* update */
        #[Route('/{id}/edit', name: 'edit_product', methods: ['GET', 'POST'])]
        public function edit_product(Request $request, Product $product, ProductRepository $productRepository): Response
        {
            $form = $this->createForm(ProductType::class, $product);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $productRepository->add($product);
                return $this->redirectToRoute('product-show_all_products', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('product/edit_product.html.twig', [
                'products' => $product,
                'form' => $form,
            ]);
        }

        /* delete */
        #[Route('/{id}', name: 'delete_product', methods: ['POST'])]
        public function delete_product(Request $request, Product $product, ProductRepository $productRepository): Response
        {
            if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
                $productRepository->remove($product);
            }

            return $this->redirectToRoute('product-show_all_products', [], Response::HTTP_SEE_OTHER);
        }

    } 
