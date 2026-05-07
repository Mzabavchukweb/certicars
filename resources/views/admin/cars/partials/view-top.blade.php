<svg viewBox="0 0 400 760" xmlns="http://www.w3.org/2000/svg" style="width:100%;max-width:360px;display:block;margin:0 auto;pointer-events:none">
    <defs>
        <linearGradient id="topBody" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#f0f0f2"/>
            <stop offset="40%" stop-color="#d8d8dc"/>
            <stop offset="100%" stop-color="#bdbdc2"/>
        </linearGradient>
        <linearGradient id="topGlass" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#9ab8d8" stop-opacity=".92"/>
            <stop offset="100%" stop-color="#6d92b8" stop-opacity=".88"/>
        </linearGradient>
        <linearGradient id="topRoof" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#c8c8cc"/>
            <stop offset="100%" stop-color="#aeaeb3"/>
        </linearGradient>
        <linearGradient id="topHood" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#e8e8ec"/>
            <stop offset="100%" stop-color="#d0d0d4"/>
        </linearGradient>
        <linearGradient id="topTrunk" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#d5d5d9"/>
            <stop offset="100%" stop-color="#c2c2c6"/>
        </linearGradient>
        <filter id="topShadow" x="-15%" y="-3%" width="130%" height="108%">
            <feGaussianBlur in="SourceAlpha" stdDeviation="8"/>
            <feOffset dy="8"/>
            <feComponentTransfer><feFuncA type="linear" slope=".22"/></feComponentTransfer>
            <feMerge><feMergeNode/><feMergeNode in="SourceGraphic"/></feMerge>
        </filter>
        <filter id="innerGlow">
            <feGaussianBlur in="SourceAlpha" stdDeviation="2"/>
            <feComponentTransfer><feFuncA type="linear" slope=".15"/></feComponentTransfer>
        </filter>
    </defs>

    {{-- Ground shadow --}}
    <ellipse cx="200" cy="742" rx="140" ry="10" fill="rgba(0,0,0,.1)"/>

    <g filter="url(#topShadow)">
        {{-- Main body outline (modern sedan/crossover proportions) --}}
        <path d="M 130 52 
                 Q 155 28 200 25 Q 245 28 270 52 
                 L 288 108 Q 298 148 303 195 
                 L 308 290 Q 310 400 308 520 
                 L 305 620 Q 300 680 270 706 
                 Q 242 722 200 723 Q 158 722 130 706 
                 Q 100 680 95 620 
                 L 92 520 Q 90 400 92 290 
                 L 97 195 Q 102 148 112 108 Z"
              fill="url(#topBody)" stroke="#8a8a8f" stroke-width="1.6"/>

        {{-- Hood panel --}}
        <path d="M 130 55 Q 155 32 200 30 Q 245 32 270 55 L 285 108 Q 292 135 296 165 L 296 192 
                 Q 200 200 104 192 L 104 165 Q 108 135 115 108 Z"
              fill="url(#topHood)" stroke="none"/>
        
        {{-- Hood center crease --}}
        <line x1="200" y1="38" x2="200" y2="188" stroke="#c5c5ca" stroke-width="1" opacity=".7"/>
        
        {{-- Hood panel lines --}}
        <path d="M 133 58 Q 165 80 200 82 Q 235 80 267 58" stroke="#b0b0b5" stroke-width=".8" fill="none" opacity=".6"/>
        <path d="M 108 145 L 292 145" stroke="#c0c0c5" stroke-width=".6" fill="none" opacity=".4"/>

        {{-- Windshield --}}
        <path d="M 114 198 Q 200 187 286 198 L 296 270 Q 200 282 104 270 Z"
              fill="url(#topGlass)" stroke="#4a6a8d" stroke-width="1.3" stroke-linejoin="round"/>
        {{-- Windshield reflection --}}
        <path d="M 145 210 Q 200 205 255 210 L 258 242 Q 200 248 142 242 Z" fill="#fff" opacity=".12"/>

        {{-- A-pillar lines --}}
        <line x1="114" y1="198" x2="108" y2="280" stroke="#5a7a9d" stroke-width="1.8"/>
        <line x1="286" y1="198" x2="292" y2="280" stroke="#5a7a9d" stroke-width="1.8"/>

        {{-- Roof with sunroof --}}
        <rect x="112" y="282" width="176" height="210" fill="url(#topRoof)" stroke="#8a8a8f" stroke-width="1" rx="12"/>
        {{-- Sunroof --}}
        <rect x="145" y="300" width="110" height="65" fill="rgba(90,97,112,.4)" rx="6" stroke="#6a6a6f" stroke-width=".8"/>
        <line x1="200" y1="300" x2="200" y2="365" stroke="rgba(60,65,76,.35)" stroke-width=".5"/>
        {{-- Roof rail hints --}}
        <line x1="112" y1="310" x2="112" y2="460" stroke="#a0a0a5" stroke-width="2" opacity=".4"/>
        <line x1="288" y1="310" x2="288" y2="460" stroke="#a0a0a5" stroke-width="2" opacity=".4"/>

        {{-- Rear window --}}
        <path d="M 104 500 Q 200 488 296 500 L 286 572 Q 200 582 114 572 Z"
              fill="url(#topGlass)" stroke="#4a6a8d" stroke-width="1.3" stroke-linejoin="round"/>
        {{-- Rear window reflection --}}
        <path d="M 145 510 Q 200 504 255 510 L 252 548 Q 200 554 148 548 Z" fill="#fff" opacity=".1"/>

        {{-- C-pillar lines --}}
        <line x1="104" y1="500" x2="108" y2="490" stroke="#5a7a9d" stroke-width="1.8"/>
        <line x1="296" y1="500" x2="292" y2="490" stroke="#5a7a9d" stroke-width="1.8"/>

        {{-- Trunk panel --}}
        <path d="M 114 578 Q 200 588 286 578 L 270 706 Q 242 722 200 723 Q 158 722 130 706 Z"
              fill="url(#topTrunk)" stroke="none"/>
        <path d="M 116 582 L 284 582" stroke="#9a9a9f" stroke-width="1"/>

        {{-- Door lines (left) --}}
        <line x1="92" y1="320" x2="112" y2="320" stroke="#a0a0a5" stroke-width="1.5"/>
        <line x1="90" y1="410" x2="112" y2="410" stroke="#a0a0a5" stroke-width="1.5"/>
        {{-- Door lines (right) --}}
        <line x1="288" y1="320" x2="308" y2="320" stroke="#a0a0a5" stroke-width="1.5"/>
        <line x1="288" y1="410" x2="310" y2="410" stroke="#a0a0a5" stroke-width="1.5"/>

        {{-- Side body highlight --}}
        <path d="M 92 260 L 92 520" stroke="#fff" stroke-width="1.5" opacity=".35"/>
        <path d="M 308 260 L 308 520" stroke="#fff" stroke-width="1.5" opacity=".35"/>

        {{-- Fender flare accents --}}
        <path d="M 88 160 Q 85 165 85 175 Q 85 185 88 190" stroke="#999" stroke-width="1.2" fill="none" opacity=".5"/>
        <path d="M 312 160 Q 315 165 315 175 Q 315 185 312 190" stroke="#999" stroke-width="1.2" fill="none" opacity=".5"/>

        {{-- Mirrors --}}
        <path d="M 78 242 Q 68 236 65 248 Q 66 260 82 258 L 88 250 Z" fill="#a8a8ad" stroke="#6a6a6f" stroke-width="1.2" stroke-linejoin="round"/>
        <path d="M 322 242 Q 332 236 335 248 Q 334 260 318 258 L 312 250 Z" fill="#a8a8ad" stroke="#6a6a6f" stroke-width="1.2" stroke-linejoin="round"/>
        {{-- Mirror glass --}}
        <path d="M 70 242 Q 68 248 70 254 L 80 252 L 80 244 Z" fill="#5a7a9d" opacity=".6"/>
        <path d="M 330 242 Q 332 248 330 254 L 320 252 L 320 244 Z" fill="#5a7a9d" opacity=".6"/>

        {{-- Headlights (modern LED DRL shape) --}}
        <path d="M 128 62 Q 140 48 162 50 L 180 70 L 168 84 Q 145 82 128 72 Z" fill="#fffce5" stroke="#bfa840" stroke-width="1.2" stroke-linejoin="round"/>
        <path d="M 272 62 Q 260 48 238 50 L 220 70 L 232 84 Q 255 82 272 72 Z" fill="#fffce5" stroke="#bfa840" stroke-width="1.2" stroke-linejoin="round"/>
        {{-- DRL LED strip --}}
        <path d="M 135 64 Q 148 55 160 56" stroke="#fff" stroke-width="2" fill="none" opacity=".9" stroke-linecap="round"/>
        <path d="M 265 64 Q 252 55 240 56" stroke="#fff" stroke-width="2" fill="none" opacity=".9" stroke-linecap="round"/>
        {{-- LED dots --}}
        <circle cx="148" cy="66" r="2.5" fill="#fff" opacity=".9"/>
        <circle cx="252" cy="66" r="2.5" fill="#fff" opacity=".9"/>

        {{-- Upper grille --}}
        <rect x="175" y="48" width="50" height="16" rx="3" fill="#1e1e22" stroke="#3a3a3e" stroke-width="1"/>
        <line x1="180" y1="53" x2="220" y2="53" stroke="#404045" stroke-width=".6"/>
        <line x1="180" y1="57" x2="220" y2="57" stroke="#404045" stroke-width=".6"/>
        <line x1="180" y1="61" x2="220" y2="61" stroke="#404045" stroke-width=".6"/>
        {{-- Badge --}}
        <circle cx="200" cy="57" r="4" fill="#2a2a2e" stroke="#7a7a7f" stroke-width=".6"/>
        <circle cx="200" cy="57" r="2" fill="#7a7a7f"/>

        {{-- Wipers --}}
        <path d="M 155 202 L 185 262" stroke="#2e2e32" stroke-width="1.2" opacity=".5" stroke-linecap="round"/>
        <path d="M 215 262 L 245 202" stroke="#2e2e32" stroke-width="1.2" opacity=".5" stroke-linecap="round"/>
        <circle cx="200" cy="196" r="2" fill="#2e2e32" opacity=".5"/>

        {{-- Taillights (modern LED bar style) --}}
        <path d="M 108 682 Q 118 676 158 680 L 156 702 Q 138 708 118 703 Q 106 698 108 682 Z" fill="#c42a2a" stroke="#7a1515" stroke-width="1.2" stroke-linejoin="round"/>
        <path d="M 292 682 Q 282 676 242 680 L 244 702 Q 262 708 282 703 Q 294 698 292 682 Z" fill="#c42a2a" stroke="#7a1515" stroke-width="1.2" stroke-linejoin="round"/>
        {{-- Light-bar connecting strip --}}
        <rect x="158" y="690" width="84" height="8" rx="2" fill="#2a2a2e" stroke="#4a4a4e" stroke-width=".8"/>
        {{-- LED strips in taillights --}}
        <path d="M 115 688 L 150 686" stroke="#ff6060" stroke-width="1.5" opacity=".8" stroke-linecap="round"/>
        <path d="M 285 688 L 250 686" stroke="#ff6060" stroke-width="1.5" opacity=".8" stroke-linecap="round"/>

        {{-- Rear diffuser hints --}}
        <path d="M 145 715 L 255 715" stroke="#6a6a6f" stroke-width=".8" opacity=".5"/>
        
        {{-- License plate --}}
        <rect x="178" y="698" width="44" height="10" rx="1.5" fill="#fff" stroke="#888" stroke-width=".6"/>
        <rect x="180" y="699" width="6" height="8" rx="1" fill="#003399" opacity=".6"/>

        {{-- Exhaust tips --}}
        <ellipse cx="155" cy="722" rx="8" ry="4" fill="#2a2a2e" stroke="#1a1a1e" stroke-width=".8"/>
        <ellipse cx="245" cy="722" rx="8" ry="4" fill="#2a2a2e" stroke="#1a1a1e" stroke-width=".8"/>
    </g>

    {{-- Labels --}}
    <g font-family="Inter,system-ui,sans-serif" font-weight="700" letter-spacing="2">
        <text x="200" y="14" text-anchor="middle" font-size="9" fill="#888">PRZÓD</text>
        <text x="200" y="756" text-anchor="middle" font-size="9" fill="#888">TYŁ</text>
        <text x="28" y="385" font-size="9" fill="#aaa" transform="rotate(-90 28 385)">LEWA</text>
        <text x="372" y="385" font-size="9" fill="#aaa" transform="rotate(90 372 385)">PRAWA</text>
    </g>
</svg>
