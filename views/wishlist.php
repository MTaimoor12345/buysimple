<div class="container">
    <h1>My Wishlist</h1>

    <div id="wishlist-content">
        <div class="empty-state">
            <i class="far fa-heart"></i>
            <p>Your wishlist is empty</p>
            <p>Start adding products to your wishlist!</p>
            <a href="<?php echo Helper::url('products'); ?>" class="btn btn-primary">Browse Products</a>
        </div>
    </div>
</div>

<script>
function showWishlist() {
    const wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
    if (wishlist.length === 0) {
        showToast('Your wishlist is empty', 'info');
        return;
    }
    window.location.href = '<?php echo Helper::url("wishlist"); ?>';
}
</script>

