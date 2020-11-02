<div class="col-100">
    <div class="container">
        <h1 class="text-center">Login to Webshop</h1>
        <?php showPostResult(); ?>
        <form method="post" class="spaced">
            <input type="text" name="username" placeholder="Username" />
            <input type="password" name="password" placeholder="Password" />
            <input type="submit" value="Log in"/>
            <a class="btn" href="/register.php">
                Register
            </a>
            <input type="hidden" name="action" value="login">
        </form>
    </div>
</div>
