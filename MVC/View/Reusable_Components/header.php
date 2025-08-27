<header>
  <div class="container nav" role="navigation" aria-label="Primary">
    <div class="brand">
      <div class="brand-logo" aria-hidden="true">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path d="M5 4h10a4 4 0 0 1 4 4v12a1 1 0 0 1-1.447.894L14 19.118l-3.553 1.776A1 1 0 0 1 9 20V6a2 2 0 0 0-2-2Z" fill="white" />
        </svg>
      </div>
      <span>Libraria</span>
    </div>

    <nav aria-label="Site">
      <ul>
        <li><a href="<?= BASE_URL ?>index.php#features">Features</a></li>
        <li><a href="<?= BASE_URL ?>index.php#pricing">Pricing</a></li>
        <li><a href="<?= BASE_URL ?>index.php#faq">FAQ</a></li>
        <li><a class="cta" href="<?= BASE_URL ?>index.php#get-started">Get Started</a></li>
      </ul>
    </nav>

    <!-- Quick Signup => to register page via router -->
    <a href="<?= BASE_URL ?>index.php?page=register" class="btn quick-signup">Quick Signup</a>

  </div>
</header>
