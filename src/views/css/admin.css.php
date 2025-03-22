<?php

/**
  * @var string $color
  * @var string $bgcolor
  */
?>/* sidebar */

.sidebar-dark-primary {
    background-color: <?= $bgcolor ?>;
}

.sidebar-dark-primary .brand-link{
    text-decoration: none;
    border-bottom: 1px solid <?= $color ?>;
}

/* button */

.btn-outline-secondary {
    --bs-btn-color: #333;
}

/* link text */

.content .form-text a {
    color: #0d6edd;
}

.navbar-light .navbar-nav .nav-link {
    color: #666;
}

.page-item.active .page-link {
    background-color: #0d6edd;
}

/* text */

.main-footer {
    color: #666;
}

/* .alert */

.alert a {
    color: var(--bs-link-color);
    font-weight: bold;
}

.alert a:hover {
    text-decoration: none !important;
}
