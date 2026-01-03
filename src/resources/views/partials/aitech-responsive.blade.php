<style>
    /*
      AITECH shared responsive overrides
      - Desktop (PC) design must not change: all rules are max-width only.
      - Targets common page primitives used across views: header/nav/main-content/forms.
    */

    @media (max-width: 920px) {
        body { overflow-x: hidden; }

        /* Header padding tends to be too wide on tablets/phones */
        .header { padding: 0 1rem; }

        /* Use mobile header height token when present */
        .header-content { height: var(--header-height-mobile, var(--header-height, 80px)); }

        /*
          Center-absolute nav is great on desktop, but can overflow on small widths.
          Convert to a scrollable row while keeping the same visual style.
        */
        .nav-links {
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            gap: 1rem;
            padding: 0.25rem 0;
        }
        .nav-links::-webkit-scrollbar { display: none; }

        /* Dropdown should never exceed viewport */
        .dropdown-content { max-width: calc(100vw - 2rem); }
    }

    @media (max-width: 768px) {
        /* Provide reliable gutters even when each screen has its own padding rules */
        .main-content { padding-left: 1rem; padding-right: 1rem; }

        /* Prevent iOS input focus zoom */
        input, select, textarea { font-size: 16px; }
    }

    @media (max-width: 480px) {
        .main-content { padding-left: 0.75rem; padding-right: 0.75rem; }
        .nav-links { gap: 0.75rem; }
        .nav-link { padding: 0.5rem 0.75rem; }
    }
</style>


