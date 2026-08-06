<div class="bottom-safe-spacer"></div>
</main>

<!-- BOTTOM NAVIGATION (Fixed) -->
<nav class="bottom-nav" id="main-bottom-nav">
    <a href="/" class="nav-item <?php echo ((ACTIVE_URL == '/') ? 'active' : '') ?>">
        <i class="ph ph-house"></i>
        <i class="ph-fill ph-house"></i>
        <span>Home</span>
    </a>
    <a href="/discover/" class="nav-item <?php echo ((ACTIVE_URL == '/discover/') ? 'active' : '') ?>">
        <i class="ph ph-compass"></i>
        <i class="ph-fill ph-compass"></i>
        <span>Discover</span>
    </a>
    <a href="/search-widget/" class="nav-item search-nav-item <?php echo ((ACTIVE_URL == '/search/') ? 'active' : '') ?>">
        <div class="search-fab">
            <i class="ph ph-magnifying-glass"></i>
        </div>
        <span>Search</span>
    </a>
    <a href="/saved/" class="nav-item <?php echo ((ACTIVE_URL == '/saved/') ? 'active' : '') ?>">
        <i class="ph ph-bookmark-simple"></i>
        <i class="ph-fill ph-bookmark-simple"></i>
        <span>Saved</span>
    </a>
    <a href="/profile/" class="nav-item <?php echo ((ACTIVE_URL == '/profile/') ? 'active' : '') ?>">
        <i class="ph ph-user"></i>
        <i class="ph-fill ph-user"></i>
        <span>Profile</span>
    </a>
</nav>
</div>

<script src="<?php echo USER_JS ?>app.js"></script>
</body>

</html>