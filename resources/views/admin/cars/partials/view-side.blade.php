<svg viewBox="0 0 800 360" xmlns="http://www.w3.org/2000/svg" style="width:100%;max-width:680px;display:block;margin:0 auto;pointer-events:none">
    <defs>
        <linearGradient id="sideBody" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#f2f2f4"/>
            <stop offset="40%" stop-color="#d8d8dc"/>
            <stop offset="100%" stop-color="#a8a8ad"/>
        </linearGradient>
        <linearGradient id="sideGlass" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#9ab8d8"/>
            <stop offset="100%" stop-color="#5d82a8"/>
        </linearGradient>
        <linearGradient id="sideRoof" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#d5d5d9"/>
            <stop offset="100%" stop-color="#c0c0c4"/>
        </linearGradient>
        <radialGradient id="sideLightF" cx=".3" cy=".5" r=".7">
            <stop offset="0%" stop-color="#fffde0"/>
            <stop offset="100%" stop-color="#c4a838"/>
        </radialGradient>
        <radialGradient id="sideLightR" cx=".7" cy=".5" r=".7">
            <stop offset="0%" stop-color="#ff9090"/>
            <stop offset="100%" stop-color="#8a1818"/>
        </radialGradient>
        <filter id="sideShadow" x="-5%" y="-5%" width="110%" height="115%">
            <feGaussianBlur in="SourceAlpha" stdDeviation="8"/>
            <feOffset dy="8"/>
            <feComponentTransfer><feFuncA type="linear" slope=".2"/></feComponentTransfer>
            <feMerge><feMergeNode/><feMergeNode in="SourceGraphic"/></feMerge>
        </filter>
    </defs>

    {{-- Ground shadow --}}
    <ellipse cx="400" cy="332" rx="350" ry="10" fill="rgba(0,0,0,.12)"/>

    <g filter="url(#sideShadow)">
        {{-- Main body silhouette (modern sports sedan) --}}
        <path d="M 60 248
                 Q 56 228 68 218
                 L 110 210
                 L 148 148
                 Q 170 112 225 96
                 L 340 82
                 Q 405 78 475 82
                 L 590 96
                 Q 638 112 658 148
                 L 692 210
                 L 732 218
                 Q 744 228 742 248
                 L 738 268
                 Q 718 282 672 282
                 L 128 282
                 Q 84 282 62 268 Z"
              fill="url(#sideBody)" stroke="#8a8a8f" stroke-width="1.5"/>

        {{-- Roofline --}}
        <path d="M 148 148 Q 170 112 225 96 L 340 82 Q 405 78 475 82 L 590 96 Q 638 112 658 148"
              fill="none" stroke="#8a8a8f" stroke-width="1.5"/>

        {{-- Body shoulder highlight lines --}}
        <path d="M 78 222 L 722 222" stroke="#fff" stroke-width="2.5" opacity=".7"/>
        <path d="M 78 236 L 722 236" stroke="#fff" stroke-width=".8" opacity=".35"/>
        
        {{-- Character line (body crease) --}}
        <path d="M 90 245 L 710 245" stroke="#c0c0c5" stroke-width="1" opacity=".6"/>

        {{-- Windows (side glass with curved greenhouse) --}}
        <path d="M 162 142 Q 185 115 240 104 L 340 92 L 408 89 L 478 92 L 575 104 Q 625 115 645 142 L 648 210 L 160 210 Z"
              fill="url(#sideGlass)" stroke="#3a6090" stroke-width="1.3" stroke-linejoin="round"/>
        {{-- Window reflection --}}
        <path d="M 200 120 Q 400 100 600 120 L 608 165 Q 400 148 192 165 Z" fill="#fff" opacity=".1"/>

        {{-- Window pillars (A, B, C with proper thickness) --}}
        <path d="M 228 96 L 205 210" stroke="#5a5a5e" stroke-width="4" fill="none" stroke-linecap="round"/>
        <path d="M 400 89 L 400 210" stroke="#5a5a5e" stroke-width="3.5" fill="none"/>
        <path d="M 588 96 L 610 210" stroke="#5a5a5e" stroke-width="4" fill="none" stroke-linecap="round"/>
        {{-- Quarter window behind C-pillar --}}
        <path d="M 590 100 L 640 138 L 645 195 L 610 210 L 592 205 Z" fill="#5580a8" opacity=".5" stroke="#3a6090" stroke-width=".8"/>

        {{-- Roof panel --}}
        <path d="M 225 96 Q 400 78 575 96 L 225 96" fill="url(#sideRoof)" stroke="none" opacity=".4"/>

        {{-- Door lines --}}
        <line x1="305" y1="215" x2="305" y2="278" stroke="#a0a0a5" stroke-width="1.8"/>
        <line x1="505" y1="215" x2="505" y2="278" stroke="#a0a0a5" stroke-width="1.8"/>
        <line x1="405" y1="218" x2="405" y2="278" stroke="#a0a0a5" stroke-width="1.3" opacity=".6"/>

        {{-- Door handles (modern flush style) --}}
        <rect x="250" y="232" width="36" height="6" rx="3" fill="#3a3a3e" stroke="#2a2a2e" stroke-width=".5"/>
        <rect x="345" y="232" width="36" height="6" rx="3" fill="#3a3a3e" stroke="#2a2a2e" stroke-width=".5"/>
        <rect x="445" y="232" width="36" height="6" rx="3" fill="#3a3a3e" stroke="#2a2a2e" stroke-width=".5"/>
        <rect x="530" y="232" width="36" height="6" rx="3" fill="#3a3a3e" stroke="#2a2a2e" stroke-width=".5"/>

        {{-- Mirror --}}
        <path d="M 232 158 L 226 138 Q 238 130 256 138 L 250 154 Z" fill="#8a8a8e" stroke="#555" stroke-width="1.2" stroke-linejoin="round"/>
        <path d="M 230 142 Q 238 134 252 142 L 248 152 L 234 154 Z" fill="#5580a8" opacity=".5"/>

        {{-- Front wheel arch --}}
        <path d="M 112 278 Q 108 225 162 218 Q 218 218 218 278" fill="#1a1a1e" stroke="#000" stroke-width="1.2"/>
        
        {{-- Front wheel (detailed alloy) --}}
        <circle cx="165" cy="275" r="52" fill="#1a1a1e" stroke="#000" stroke-width="2"/>
        <circle cx="165" cy="275" r="44" fill="#222"/>
        <circle cx="165" cy="275" r="38" fill="#2e2e32"/>
        {{-- Alloy face --}}
        <circle cx="165" cy="275" r="32" fill="#888"/>
        <circle cx="165" cy="275" r="26" fill="#6a6a6e"/>
        <circle cx="165" cy="275" r="7" fill="#333"/>
        {{-- 5-spoke pattern --}}
        <g stroke="#444" stroke-width="4" stroke-linecap="round" fill="none">
            <line x1="165" y1="249" x2="165" y2="301"/>
            <line x1="190" y1="262" x2="140" y2="288"/>
            <line x1="190" y1="288" x2="140" y2="262"/>
            <line x1="180" y1="250" x2="150" y2="300"/>
            <line x1="180" y1="300" x2="150" y2="250"/>
        </g>
        {{-- Tire sidewall text hint --}}
        <path d="M 130 240 Q 165 222 200 240" fill="none" stroke="#333" stroke-width=".5" opacity=".5"/>
        {{-- Brake caliper hint --}}
        <rect x="148" y="270" width="14" height="8" rx="1.5" fill="#c42a2a" opacity=".6"/>

        {{-- Rear wheel arch --}}
        <path d="M 590 278 Q 594 225 648 218 Q 702 218 698 278" fill="#1a1a1e" stroke="#000" stroke-width="1.2"/>
        
        {{-- Rear wheel (detailed alloy) --}}
        <circle cx="645" cy="275" r="52" fill="#1a1a1e" stroke="#000" stroke-width="2"/>
        <circle cx="645" cy="275" r="44" fill="#222"/>
        <circle cx="645" cy="275" r="38" fill="#2e2e32"/>
        <circle cx="645" cy="275" r="32" fill="#888"/>
        <circle cx="645" cy="275" r="26" fill="#6a6a6e"/>
        <circle cx="645" cy="275" r="7" fill="#333"/>
        <g stroke="#444" stroke-width="4" stroke-linecap="round" fill="none">
            <line x1="645" y1="249" x2="645" y2="301"/>
            <line x1="670" y1="262" x2="620" y2="288"/>
            <line x1="670" y1="288" x2="620" y2="262"/>
            <line x1="660" y1="250" x2="630" y2="300"/>
            <line x1="660" y1="300" x2="630" y2="250"/>
        </g>
        <path d="M 610 240 Q 645 222 680 240" fill="none" stroke="#333" stroke-width=".5" opacity=".5"/>
        <rect x="628" y="270" width="14" height="8" rx="1.5" fill="#c42a2a" opacity=".6"/>

        {{-- Headlight --}}
        <path d="M 62 225 L 92 220 L 96 248 L 66 252 Z"
              fill="url(#sideLightF)" stroke="#9a8028" stroke-width="1.2" stroke-linejoin="round"/>
        {{-- LED DRL strip --}}
        <path d="M 68 228 L 90 226" stroke="#fff" stroke-width="2" opacity=".9" stroke-linecap="round"/>

        {{-- Taillight --}}
        <path d="M 718 225 L 740 220 L 738 250 L 716 252 Z"
              fill="url(#sideLightR)" stroke="#5a1515" stroke-width="1.2" stroke-linejoin="round"/>
        {{-- LED strip --}}
        <path d="M 722 232 L 735 230" stroke="#ffc0c0" stroke-width="1.5" opacity=".8" stroke-linecap="round"/>

        {{-- Side turn indicator --}}
        <rect x="215" y="240" width="14" height="4" rx="1" fill="#ff9d3a" stroke="#a05a10" stroke-width=".5"/>

        {{-- Lower body panel lines --}}
        <path d="M 108 214 L 118 268" stroke="#a0a0a5" stroke-width="1.2" opacity=".5"/>
        <path d="M 698 214 L 688 268" stroke="#a0a0a5" stroke-width="1.2" opacity=".5"/>

        {{-- Side skirt --}}
        <path d="M 218 280 L 590 280" stroke="#5a5a5e" stroke-width="2" opacity=".6"/>
        <path d="M 225 284 L 582 284" stroke="#6a6a6e" stroke-width="1" opacity=".4"/>

        {{-- Ground clearance --}}
        <path d="M 78 282 L 112 282 M 218 282 L 590 282 M 698 282 L 732 282" stroke="#6a6a6e" stroke-width="1.2" opacity=".5"/>

        {{-- Exhaust tip (side view) --}}
        <ellipse cx="738" cy="272" rx="5" ry="6" fill="#2a2a2e" stroke="#1a1a1e" stroke-width=".8"/>
    </g>

    <g font-family="Inter,system-ui,sans-serif" font-weight="700" letter-spacing="2">
        <text x="100" y="28" text-anchor="start" font-size="9" fill="#aaa">← PRZÓD</text>
        <text x="700" y="28" text-anchor="end" font-size="9" fill="#aaa">TYŁ →</text>
    </g>
</svg>
