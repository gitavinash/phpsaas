<?php
if ($site_access_type != 'customer') {
?>
<!-- Leads Section -->
<li class="header-section-name">
    <p>Leads<span>Manage leads.</span></p>
</li>
<li <?php echo ((strpos(ADMIN_BASE_URL, '/leads/') !== false) ? 'class="active"' : '') ?>>
    <a href="leads/"><i class="fi fi-rr-chart-network"></i> Leads</a>
    <div class="side-menu">
        <ul>
            <li><a href="leads/list/"><i class="fi-rr-apps-sort"></i> All Leads</a></li>
            <li><a href="leads/add/"><i class="fi fi-rr-add"></i> Add Lead</a></li>
            <li><a href="leads/list/?leadStatus=1"><i class="fi-rr-check"></i> Confirmed</a></li>
            <li><a href="leads/list/?leadStatus=2"><i class="fi-rr-hourglass-end"></i> Pending</a></li>
            <li><a href="leads/list/?leadStatus=3"><i class="fi-rr-comment"></i> Follow Ups</a></li>
            <li><a href="leads/list/?leadStatus=4"><i class="fi-rr-cross-circle"></i> Cancelled</a></li>
        </ul>
    </div>
</li>

<!-- Visa Packages -->
<li class="header-section-name">
    <p>VISA Packages<span>Visa packages & countries.</span></p>
</li>
<li <?php echo ((strpos(ADMIN_BASE_URL, '/visa_packages/') !== false) ? 'class="active"' : '') ?>>
    <a href="visa_packages/"><i class="fi fi-rr-passport"></i> Packages</a>
    <div class="side-menu">
        <ul>
            <li><a href="visa_packages/list/"><i class="fi-rr-apps-sort"></i> All Packages</a></li>
            <li><a href="visa_packages/add/"><i class="fi fi-rr-add"></i> Add Package</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="visa_packages/list/"><i class="fi-rr-apps-sort"></i> All Packages</a></li>
            <li><a href="visa_packages/add/"><i class="fi fi-rr-add"></i> Add Package</a></li>
        </ul>
    </div>
</li>
<li <?php echo ((strpos(ADMIN_BASE_URL, '/country/') !== false) ? 'class="active"' : '') ?>>
    <a href="country/"><i class="fi fi-rr-flag"></i> Countries</a>
    <div class="side-menu">
        <ul>
            <li><a href="country/list/"><i class="fi-rr-apps-sort"></i> All Countries</a></li>
            <li><a href="country/add/"><i class="fi fi-rr-add"></i> Add Country</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="country/list/"><i class="fi-rr-apps-sort"></i> All Countries</a></li>
            <li><a href="country/add/"><i class="fi fi-rr-add"></i> Add Country</a></li>
        </ul>
    </div>
</li>
<!-- 
<li class="header-section-name">
    <p>Website Manager<span>Pages, design, forms & more</span></p>
</li>

<li <?php echo ((strpos(ADMIN_BASE_URL, '/pages/') !== false) ? 'class="active"' : '') ?>>
    <a href="pages/"><i class="fi fi-rr-document"></i> Pages</a>
    <div class="side-menu">
        <ul>
            <li><a href="pages/list/"><i class="fi-rr-apps-sort"></i> All Pages</a></li>
            <li><a href="pages/add/"><i class="fi fi-rr-add"></i> Add Page</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="pages/list/"><i class="fi-rr-apps-sort"></i> All Pages</a></li>
            <li><a href="pages/add/"><i class="fi fi-rr-add"></i> Add Page</a></li>
        </ul>
    </div>
</li>

<li <?php echo ((strpos(ADMIN_BASE_URL, '/menus/') !== false) ? 'class="active"' : '') ?>>
    <a href="menus/"><i class="fi fi-rr-list"></i> Menus</a>
    <div class="side-menu">
        <ul>
            <li><a href="menus/list/"><i class="fi-rr-apps-sort"></i> All Menus</a></li>
            <li><a href="menus/add/"><i class="fi fi-rr-add"></i> Add Menu</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="menus/list/"><i class="fi-rr-apps-sort"></i> All Menus</a></li>
            <li><a href="menus/add/"><i class="fi fi-rr-add"></i> Add Menu</a></li>
        </ul>
    </div>
</li>

<li <?php echo ((strpos(ADMIN_BASE_URL, '/sliders/') !== false) ? 'class="active"' : '') ?>>
    <a href="sliders/"><i class="fi fi-rr-images"></i> Hero Sliders</a>
    <div class="side-menu">
        <ul>
            <li><a href="sliders/list/"><i class="fi-rr-apps-sort"></i> All Sliders</a></li>
            <li><a href="sliders/add/"><i class="fi fi-rr-add"></i> Add Slider</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="sliders/list/"><i class="fi-rr-apps-sort"></i> All Sliders</a></li>
            <li><a href="sliders/add/"><i class="fi fi-rr-add"></i> Add Slider</a></li>
        </ul>
    </div>
</li>

<li <?php echo ((strpos(ADMIN_BASE_URL, '/testimonials/') !== false) ? 'class="active"' : '') ?>>
    <a href="testimonials/"><i class="fi fi-rr-comment-alt"></i> Testimonials</a>
    <div class="side-menu">
        <ul>
            <li><a href="testimonials/list/"><i class="fi-rr-apps-sort"></i> All Testimonials</a></li>
            <li><a href="testimonials/add/"><i class="fi fi-rr-add"></i> Add Testimonial</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="testimonials/list/"><i class="fi-rr-apps-sort"></i> All Testimonials</a></li>
            <li><a href="testimonials/add/"><i class="fi fi-rr-add"></i> Add Testimonial</a></li>
        </ul>
    </div>
</li>

<li <?php echo ((strpos(ADMIN_BASE_URL, '/forms/') !== false) ? 'class="active"' : '') ?>>
    <a href="forms/"><i class="fi fi-rr-envelope"></i> Contact Form</a>
    <div class="side-menu">
        <ul>
            <li><a href="forms/entries/"><i class="fi-rr-inbox"></i> Form Entries</a></li>
            <li><a href="forms/settings/"><i class="fi fi-rr-settings"></i> Settings</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="forms/entries/"><i class="fi-rr-inbox"></i> Form Entries</a></li>
            <li><a href="forms/settings/"><i class="fi fi-rr-settings"></i> Settings</a></li>
        </ul>
    </div>
</li>

<li <?php echo ((strpos(ADMIN_BASE_URL, '/faq/') !== false) ? 'class="active"' : '') ?>>
    <a href="faq/"><i class="fi fi-rr-question"></i> FAQs</a>
    <div class="side-menu">
        <ul>
            <li><a href="faq/list/"><i class="fi-rr-apps-sort"></i> All FAQs</a></li>
            <li><a href="faq/add/"><i class="fi fi-rr-add"></i> Add FAQ</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="faq/list/"><i class="fi-rr-apps-sort"></i> All FAQs</a></li>
            <li><a href="faq/add/"><i class="fi fi-rr-add"></i> Add FAQ</a></li>
        </ul>
    </div>
</li>

<li <?php echo ((strpos(ADMIN_BASE_URL, '/gallery/') !== false) ? 'class="active"' : '') ?>>
    <a href="gallery/"><i class="fi fi-rr-picture"></i> Gallery</a>
    <div class="side-menu">
        <ul>
            <li><a href="gallery/list/"><i class="fi-rr-apps-sort"></i> All galleries</a></li>
            <li><a href="gallery/add/"><i class="fi fi-rr-add"></i> Add gallery</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="gallery/list/"><i class="fi-rr-apps-sort"></i> All galleries</a></li>
            <li><a href="gallery/add/"><i class="fi fi-rr-add"></i> Add gallery</a></li>
        </ul>
    </div>
</li> -->

<li class="header-section-name">
    <p>Blog Center<span>News & Updates</span></p>
</li>

<li <?php echo ((strpos(ADMIN_BASE_URL, '/blog/') !== false) ? 'class="active"' : '') ?>>
    <a href="blog/"><i class="fi fi-rr-newspaper"></i> Blogs</a>
    <div class="side-menu">
        <ul>
            <li><a href="blog/list/"><i class="fi-rr-apps-sort"></i> All Blogs</a></li>
            <li><a href="blog/add/"><i class="fi fi-rr-add"></i> Add Blog</a></li>
            <li><a href="blog/categories/"><i class="fi fi-rr-filter-list"></i> Categories</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="blog/list/"><i class="fi-rr-apps-sort"></i> All Blogs</a></li>
            <li><a href="blog/add/"><i class="fi fi-rr-add"></i> Add Blog</a></li>
            <li><a href="blog/categories/"><i class="fi fi-rr-filter-list"></i> Categories</a></li>
        </ul>
    </div>
</li>

<!-- <li <?php echo ((strpos(ADMIN_BASE_URL, '/news/') !== false) ? 'class="active"' : '') ?>>
    <a href="news/"><i class="fi fi-rr-bullhorn"></i> News</a>
    <div class="side-menu">
        <ul>
            <li><a href="news/list/"><i class="fi-rr-apps-sort"></i> All news</a></li>
            <li><a href="news/add/"><i class="fi fi-rr-add"></i> Add news</a></li>
            <li><a href="news/category/"><i class="fi fi-rr-filter-list"></i> Categories</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="news/list/"><i class="fi-rr-apps-sort"></i> All news</a></li>
            <li><a href="news/add/"><i class="fi fi-rr-add"></i> Add news</a></li>
            <li><a href="news/category/"><i class="fi fi-rr-filter-list"></i> Categories</a></li>
        </ul>
    </div>
</li> -->

<li class="header-section-name">
    <p>Business Hub<span>Control Your Business</span></p>
</li>
<li <?php echo ((strpos(ADMIN_BASE_URL, '/policy/') !== false) ? 'class="active"' : '') ?>>
    <a href="policy/"><i class="fi fi-rr-memo"></i> Policies</a>
    <div class="side-menu">
        <ul>
            <li><a href="policy/list/"><i class="fi-rr-apps-sort"></i> All Policies</a></li>
            <li><a href="policy/add/"><i class="fi fi-rr-add"></i> Add Policy</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="policy/list/"><i class="fi-rr-apps-sort"></i> All Policies</a></li>
            <li><a href="policy/add/"><i class="fi fi-rr-add"></i> Add Policy</a></li>
        </ul>
    </div>
</li>

<li <?php echo ((strpos(ADMIN_BASE_URL, '/payment/') !== false) ? 'class="active"' : '') ?>>
    <a href="payment/"><i class="fi fi-rr-indian-rupee-sign"></i> Payments</a>
    <div class="side-menu">
        <ul>
            <li><a href="payment/list/"><i class="fi-rr-apps-sort"></i> All Methods</a></li>
            <li><a href="payment/add/"><i class="fi fi-rr-add"></i> Add Method</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="payment/list/"><i class="fi-rr-apps-sort"></i> All Methods</a></li>
            <li><a href="payment/add/"><i class="fi fi-rr-add"></i> Add Method</a></li>
        </ul>
    </div>
</li>

<li <?php echo ((strpos(ADMIN_BASE_URL, '/company/') !== false) ? 'class="active"' : '') ?>>
    <a href="company/"><i class="fi fi-rr-building"></i> Company</a>
    <div class="side-menu">
        <ul>
            <li><a href="company/list/"><i class="fi-rr-apps-sort"></i> All Companies</a></li>
            <li><a href="company/add/"><i class="fi fi-rr-add"></i> Add Company</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="company/list/"><i class="fi-rr-apps-sort"></i> All Companies</a></li>
            <li><a href="company/add/"><i class="fi fi-rr-add"></i> Add Company</a></li>
        </ul>
    </div>
</li>
<?php
} else {
?>
<li class="header-section-name">
    <p>Application<span>Track your VISA Applications.</span></p>
</li>
<li <?php echo ((strpos(ADMIN_BASE_URL, '/visa_applications/') !== false) ? 'class="active"' : '') ?>>
    <a href="visa_applications/"><i class="fi fi-rr-passport"></i>Applications</a>
    <div class="side-menu">
        <ul>
            <li><a href="visa_applications/list/"><i class="fi-rr-apps-sort"></i> All Applications</a></li>
            <li><a href="/start-application/" target="_blank"><i class="fi fi-rr-add"></i> Apply New</a></li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li><a href="visa_applications/list/"><i class="fi-rr-apps-sort"></i> All Applications</a></li>
            <li><a href="/start-application/" target="_blank"><i class="fi fi-rr-add"></i> Apply New</a></li>
        </ul>
    </div>
</li>
<?php
}
?>