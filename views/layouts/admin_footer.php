            </div>
        </main>
    </div>

    <script src="<?php echo Helper::asset('js/main.js'); ?>?v=3"></script>
    <script src="<?php echo Helper::asset('js/admin.js'); ?>"></script>
    <script>
        function switchUploadTab(tab) {
            // Hide all tabs
            document.querySelectorAll('.upload-tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Show selected tab
            const tabContent = document.getElementById(tab + '-tab');
            if (tabContent) {
                tabContent.classList.add('active');
            }
            if (event && event.target) {
                event.target.classList.add('active');
            }
            
            // Handle required attribute on image URL field
            const imageUrlInput = document.getElementById('image_url');
            const imageFileInput = document.getElementById('image_file');
            
            if (tab === 'url') {
                // URL tab selected - make URL required, file not required
                if (imageUrlInput) {
                    imageUrlInput.required = true;
                }
                if (imageFileInput) {
                    imageFileInput.value = '';
                    imageFileInput.required = false;
                }
                const preview = document.getElementById('image-preview');
                if (preview) {
                    preview.style.display = 'none';
                }
            } else if (tab === 'file') {
                // File tab selected - make file required, URL not required
                if (imageUrlInput) {
                    imageUrlInput.required = false;
                }
                if (imageFileInput) {
                    imageFileInput.required = true;
                }
            }
        }
        
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-img').src = e.target.result;
                    document.getElementById('image-preview').style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Product images preview (multiple)
        document.addEventListener('DOMContentLoaded', function() {
            const createInput = document.getElementById('product_images');
            const editInput = document.getElementById('product_images_edit');

            function handleProductImagesPreview(input, previewId) {
                const preview = document.getElementById(previewId);
                if (!preview || !input) return;

                input.addEventListener('change', function() {
                    const files = Array.from(input.files || []);
                    preview.innerHTML = '';

                    if (!files.length) {
                        preview.style.display = 'none';
                        return;
                    }

                    files.forEach(file => {
                        if (!file.type.startsWith('image/')) return;

                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const wrapper = document.createElement('div');
                            wrapper.className = 'product-image-thumb';
                            wrapper.innerHTML = `<img src=\"${e.target.result}\" alt=\"Preview\">`;
                            preview.appendChild(wrapper);
                        };
                        reader.readAsDataURL(file);
                    });

                    preview.style.display = 'grid';
                });
            }

            handleProductImagesPreview(createInput, 'product-images-preview');
            handleProductImagesPreview(editInput, 'product-images-preview-edit');
        });
    </script>
</body>
</html>

