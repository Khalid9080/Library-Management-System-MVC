<!-- MVC/view/reusable_component/main.php -->
<main id="main">
  <!-- Hero -->
  <section class="hero">
    <div class="container hero-grid">
      <div>
        <span class="eyebrow" aria-label="Badge: New in 2025">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.73 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z" fill="currentColor" />
          </svg>
          New in 2025 — Faster checkouts, smarter insights
        </span>
        <h1>Run your library like a pro.</h1>
        <p class="lead">A modern Library Management System to catalog collections, manage members, track circulation,
          and pull insights — all in one accessible dashboard.</p>
        <div class="hero-actions">
          <a class="btn primary" href="#get-started" aria-label="Get started with the Library Management System">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <path d="M8 5v14l11-7L8 5z" fill="currentColor" />
            </svg>
            Get Started
          </a>
          <a class="btn ghost" href="#demo" aria-label="View live demo">Live Demo</a>
        </div>
        <div class="meta">
          <div class="pill" aria-label="Uptime">99.99% uptime</div>
          <div class="pill" aria-label="Accessibility">WCAG AA accessible</div>
          <div class="pill" aria-label="Deployment">Cloud & on-prem</div>
        </div>
      </div>

      <aside class="card-preview" aria-label="Product preview">
        <form class="search" role="search" aria-label="Catalog search (demo)">
          <input type="search" placeholder="Search books, authors, ISBN…" aria-label="Search query" />
          <button type="button" aria-label="Search">Search</button>
        </form>
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: .6rem; margin-top: .8rem;">
          <div class="feature">
            <div class="icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path fill="white" d="M3 6a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V6zm5 1v10h2V7H8zm6 0v10h2V7h-2z" />
              </svg>
            </div>
            <h3>One-click Checkouts</h3>
            <p>Scan, loan, and return in seconds with smart due-date rules.</p>
          </div>
          <div class="feature">
            <div class="icon" aria-hidden="true" style="background: linear-gradient(135deg, var(--accent), #80ffea);">
              <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path fill="white" d="M12 3a9 9 0 1 0 0 18 9 9 0 1 0 0-18Zm1 13h-2v-2h2v2Zm0-4h-2V7h2v5Z" />
              </svg>
            </div>
            <h3>Smart Overdue Alerts</h3>
            <p>Automated reminders via email/SMS reduce late returns by 42%.</p>
          </div>
        </div>
      </aside>
    </div>

    <div class="container trust" aria-label="Trusted by">
      <small>Trusted by public, school, and university libraries worldwide</small>
      <div class="logo-row" aria-hidden="true">
        <div class="logo">Atlas U</div>
        <div class="logo">Riverside</div>
        <div class="logo">Northway</div>
        <div class="logo">Cobalt</div>
        <div class="logo">Greenfield</div>
      </div>
    </div>
  </section>

  <!-- Features -->
  <section id="features" class="section">
    <div class="container">
      <h2>Everything you need to run a modern library</h2>
      <p class="section-lead">From acquisition to analytics, Libraria keeps your team in sync and your patrons delighted.</p>

      <div class="features" role="list">
        <article class="feature" role="listitem">
          <div class="icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path fill="white" d="M4 6a2 2 0 0 1 2-2h8l6 6v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6zm10 0v4h4l-4-4z" />
            </svg>
          </div>
          <h3>Cataloging</h3>
          <p>Support for MARC21, ISBN lookup, custom fields, and batch import.</p>
        </article>

        <article class="feature" role="listitem">
          <div class="icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path fill="white" d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm-9 9a9 9 0 1 1 18 0H3z" />
            </svg>
          </div>
          <h3>Member Management</h3>
          <p>Profiles, role-based access, age bands, and family accounts.</p>
        </article>

        <article class="feature" role="listitem">
          <div class="icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path fill="white" d="M3 5h18v2H3V5Zm0 6h18v2H3v-2Zm0 6h18v2H3v-2Z" />
            </svg>
          </div>
          <h3>Circulation</h3>
          <p>Loans, holds, renewals, fines, and automated policies that fit your rules.</p>
        </article>

        <article class="feature" role="listitem">
          <div class="icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path fill="white" d="M3 13h4v8H3v-8Zm7-6h4v14h-4V7Zm7 3h4v11h-4V10Z" />
            </svg>
          </div>
          <h3>Analytics</h3>
          <p>Live dashboards for circulation, collection turnover, and patron activity.</p>
        </article>
      </div>

      <div class="stats" aria-label="Key metrics">
        <div class="stat"><b>2M+</b><span>items indexed</span></div>
        <div class="stat"><b>120k</b><span>active patrons</span></div>
        <div class="stat"><b>42%</b><span>fewer overdues</span></div>
        <div class="stat"><b>5 min</b><span>to first checkout</span></div>
      </div>
    </div>
  </section>

  <!-- Testimonial / CTA -->
  <section class="section" id="demo">
    <div class="container" style="display:grid; gap: 1rem;">
      <blockquote class="quote">
        <p>“Libraria streamlined everything — we moved 80,000 records and were running checkouts the same day.”</p>
        <footer>— Danielle, Head Librarian, Riverside Public</footer>
      </blockquote>

      <div id="get-started" class="cta-wide">
        <div>
          <h2>Start your free trial</h2>
          <p class="section-lead">Spin up a sandbox with sample data or connect your catalog when you’re ready. No credit card required.</p>
        </div>
        <div>
          <form class="search" aria-label="Get started form">
            <input type="email" placeholder="Work email" aria-label="Email address" required />
            <button type="button" aria-label="Create account">Create Account</button>
          </form>
          <small style="color: var(--muted); display:block; margin-top:.5rem;">By continuing you agree to our <a href="#">Terms</a> & <a href="#">Privacy</a>.</small>
        </div>
      </div>
    </div>
  </section>

  <!-- Pricing (static demo) -->
  <section id="pricing" class="section">
    <div class="container">
      <h2>Simple, usage-based pricing</h2>
      <p class="section-lead">Choose a plan that grows with your community.</p>

      <div class="features" role="list">
        <article class="feature" role="listitem">
          <h3>Community</h3>
          <p><b>Free</b> — small clubs & pilots</p>
          <ul>
            <li>Up to 2 librarians</li>
            <li>5,000 items</li>
            <li>Email support</li>
          </ul>
        </article>

        <article class="feature" role="listitem">
          <h3>Public</h3>
          <p><b>$99/mo</b> — growing libraries</p>
          <ul>
            <li>Unlimited librarians</li>
            <li>50,000 items</li>
            <li>SMS reminders</li>
          </ul>
        </article>

        <article class="feature" role="listitem">
          <h3>Campus</h3>
          <p><b>$299/mo</b> — universities</p>
          <ul>
            <li>SAML SSO</li>
            <li>Advanced analytics</li>
            <li>Priority support</li>
          </ul>
        </article>

        <article class="feature" role="listitem">
          <h3>Enterprise</h3>
          <p><b>Let’s talk</b> — multi-branch</p>
          <ul>
            <li>On-prem deploy</li>
            <li>Custom SLAs</li>
            <li>White-glove migration</li>
          </ul>
        </article>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section id="faq" class="section">
    <div class="container">
      <h2>Frequently asked questions</h2>
      <div class="features" role="list">
        <article class="feature" role="listitem">
          <h3>Can I import existing records?</h3>
          <p>Yes — upload MARC21, CSV, or connect via API. We also provide assisted migration.</p>
        </article>
        <article class="feature" role="listitem">
          <h3>Do you support barcodes & scanners?</h3>
          <p>Absolutely. Libraria works with common barcode formats and USB/Bluetooth scanners.</p>
        </article>
        <article class="feature" role="listitem">
          <h3>Is there an OPAC for patrons?</h3>
          <p>Yes. Offer a branded catalog, holds, and account self-service for patrons.</p>
        </article>
        <article class="feature" role="listitem">
          <h3>How secure is the system?</h3>
          <p>Role-based access, audit logs, encryption in transit & at rest, plus SSO on higher tiers.</p>
        </article>
      </div>
    </div>
  </section>
</main>
