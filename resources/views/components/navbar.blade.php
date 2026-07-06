<!-- TOP BAR -->
<div class="topbar">
    <div class="container">
        <div class="topbar__left">Mon–Sat: 10:00 AM – 7:00 PM &nbsp;•&nbsp; RCI-Certified Audiologists</div>
        <div class="topbar__social">
            <a href="tel:9811418578">📞 +91-9811418578</a>
            <a href="https://www.facebook.com/FairfieldClinicDelhi/" target="_blank" rel="noopener" aria-label="Facebook">Facebook</a>
            <a href="https://www.instagram.com/fairfieldhearingclinics/" target="_blank" rel="noopener" aria-label="Instagram">Instagram</a>
            <a href="https://www.youtube.com/@FairfieldHearingClinic" target="_blank" rel="noopener" aria-label="YouTube">YouTube</a>
        </div>
    </div>
</div>

<!-- HEADER / NAV -->
<header class="header" x-data="{ isOpen: false }">
    <div class="container nav">
        <a href="/" class="brand" aria-label="Fairfield Hearing Clinics home">
            <img src="/assets/img/logo.jpeg" alt="Fairfield Hearing Clinics logo" style="height: 78px; width: auto;" />
        </a>
        <nav aria-label="Primary">
            <ul class="nav__menu" :class="{ 'open': isOpen }" id="navMenu" :style="{ display: isOpen ? 'block' : '' }">
                <li>
                    <a href="/" :aria-current="request()->is('/') ? 'page' : undefined" @click="isOpen = false">
                        Home
                    </a>
                </li>
                <li>
                    <a href="/about" :aria-current="request()->is('about') ? 'page' : undefined" @click="isOpen = false">
                        About
                    </a>
                </li>
                <li>
                    <a href="{{ request()->is('/') ? '#services' : '/#services' }}" @click="isOpen = false">
                        Services
                    </a>
                </li>
                <li>
                    <a href="{{ request()->is('/') ? '#devices' : '/#devices' }}" @click="isOpen = false">
                        Hearing Aids
                    </a>
                </li>
                <li>
                    <a href="{{ request()->is('/') ? '#clinics' : '/#clinics' }}" @click="isOpen = false">
                        Clinics
                    </a>
                </li>
                <li>
                    <a href="{{ request()->is('/') ? '#faq' : '/#faq' }}" @click="isOpen = false">
                        FAQ
                    </a>
                </li>
                <li>
                    <a href="/blogs" :aria-current="request()->is('blogs*') ? 'page' : undefined" @click="isOpen = false">
                        Blogs
                    </a>
                </li>
            </ul>
        </nav>
        <div class="nav__cta">
            <a href="/book-a-test" class="btn btn--lime">
                Book Free Test
            </a>
            <button
                class="nav__toggle"
                id="navToggle"
                aria-label="Open menu"
                :aria-expanded="isOpen"
                aria-controls="navMenu"
                @click="isOpen = !isOpen"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2">
                    <path d="M3 6h18M3 12h18M3 18h18" />
                </svg>
            </button>
        </div>
    </div>
</header>
