<?php
class CategoryController {
    public function show($slug) {
        $categoryModel = new Category();
        $productModel = new Product();
        $subCategoryModel = new SubCategory();
        
        $category = $categoryModel->getBySlug($slug);
        
        if (!$category) {
            http_response_code(404);
            echo "Category not found";
            return;
        }
        
        // Get products that don't belong to any sub-category (direct category products)
        $products = $productModel->getByCategoryWithoutSubCategory($category['id']);
        $categories = $categoryModel->getAll();
        $subCategories = $categoryModel->getSubCategories($category['id']);
        
        // Get products for each sub-category
        $subCategoriesWithProducts = [];
        foreach ($subCategories as $subCat) {
            $subCatProducts = $productModel->getBySubCategory($subCat['id']);
            $subCategoriesWithProducts[] = [
                'subCategory' => $subCat,
                'products' => $subCatProducts
            ];
        }
        
        $this->view('category/show', [
            'category' => $category,
            'products' => $products,
            'categories' => $categories,
            'subCategoriesWithProducts' => $subCategoriesWithProducts
        ]);
    }
    
    public function showSubCategory($categorySlug, $subCategorySlug) {
        $categoryModel = new Category();
        $subCategoryModel = new SubCategory();
        $productModel = new Product();
        
        $category = $categoryModel->getBySlug($categorySlug);
        
        if (!$category) {
            http_response_code(404);
            echo "Category not found";
            return;
        }
        
        $subCategory = $subCategoryModel->getBySlug($subCategorySlug, $category['id']);
        
        if (!$subCategory) {
            http_response_code(404);
            echo "Sub-category not found";
            return;
        }
        
        // Get products by sub-category
        $products = $productModel->getBySubCategory($subCategory['id']);
        $categories = $categoryModel->getAll();
        $subCategories = $categoryModel->getSubCategories($category['id']);
        
        $this->view('category/show', [
            'category' => $category,
            'subCategory' => $subCategory,
            'products' => $products,
            'categories' => $categories,
            'subCategories' => $subCategories
        ]);
    }
    
    protected function view($view, $data = []) {
        extract($data);
        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . "/../views/{$view}.php";
        require __DIR__ . '/../views/layouts/footer.php';
    }
}

