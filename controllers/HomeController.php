<?php
class HomeController {
    public function index() {
        $productModel = new Product();
        $categoryModel = new Category();
        $heroSlideModel = new HeroSlide();
        $collectionModel = new Collection();
        
        $featuredProducts = $productModel->getFeatured(8);
        $categories = $categoryModel->getAll();
        
        // Get hero slides from admin, if none then use defaults
        $heroSlides = $heroSlideModel->getAll();
        
        // Get collections
        $collections = $collectionModel->getAll();
        
        // If no admin slides, use default images
        if (empty($heroSlides)) {
            $heroSlides = [
                [
                    'id' => 1,
                    'image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1920&h=800&fit=crop',
                    'button_text' => 'Buy Now',
                    'button_link' => Helper::url('products')
                ],
                [
                    'id' => 2,
                    'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=1920&h=800&fit=crop',
                    'button_text' => 'Buy Now',
                    'button_link' => Helper::url('products')
                ],
                [
                    'id' => 3,
                    'image' => 'https://images.unsplash.com/photo-1607082349566-187342175e2f?w=1920&h=800&fit=crop',
                    'button_text' => 'Buy Now',
                    'button_link' => Helper::url('products')
                ]
            ];
        } else {
            // Process admin slides - convert button_link and image_link to full URL if needed
            foreach ($heroSlides as &$slide) {
                if (!empty($slide['button_link']) && !filter_var($slide['button_link'], FILTER_VALIDATE_URL)) {
                    $slide['button_link'] = Helper::url(ltrim($slide['button_link'], '/'));
                }
                if (!empty($slide['image_link']) && !filter_var($slide['image_link'], FILTER_VALIDATE_URL)) {
                    $slide['image_link'] = Helper::url(ltrim($slide['image_link'], '/'));
                }
            }
        }
        
        // Process collections - convert links to full URL if needed
        foreach ($collections as &$collection) {
            if (!empty($collection['link']) && !filter_var($collection['link'], FILTER_VALIDATE_URL)) {
                $collection['link'] = Helper::url(ltrim($collection['link'], '/'));
            }
        }
        
        $this->view('home', [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'heroSlides' => $heroSlides,
            'collections' => $collections
        ]);
    }
    
    protected function view($view, $data = []) {
        extract($data);
        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . "/../views/{$view}.php";
        require __DIR__ . '/../views/layouts/footer.php';
    }
}

