<svg viewBox="0 0 600 440" xmlns="http://www.w3.org/2000/svg" style="width:100%;max-width:520px;display:block;margin:0 auto;pointer-events:none">
    <defs>
        <linearGradient id="rearBody" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#f2f2f4"/>
            <stop offset="50%" stop-color="#d5d5d9"/>
            <stop offset="100%" stop-color="#b0b0b5"/>
        </linearGradient>
        <linearGradient id="rearGlass" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#8bb0d6"/>
            <stop offset="100%" stop-color="#5580a8"/>
        </linearGradient>
        <radialGradient id="tailLightL" cx=".4" cy=".5" r=".6">
            <stop offset="0%" stop-color="#ff8a8a"/>
            <stop offset="60%" stop-color="#c42a2a"/>
            <stop offset="100%" stop-color="#6a1010"/>
        </radialGradient>
        <radialGradient id="tailLightR" cx=".6" cy=".5" r=".6">
            <stop offset="0%" stop-color="#ff8a8a"/>
            <stop offset="60%" stop-color="#c42a2a"/>
            <stop offset="100%" stop-color="#6a1010"/>
        </radialGradient>
        <filter id="rearShadow" x="-10%" y="-5%" width="120%" height="115%">
            <feGaussianBlur in="SourceAlpha" stdDeviation="8"/>
            <feOffset dy="8"/>
            <feComponentTransfer><feFuncA type="linear" slope=".2"/></feComponentTransfer>
            <feMerge><feMergeNode/><feMergeNode in="SourceGraphic"/></feMerge>
        </filter>
    </defs>

    <ellipse cx="300" cy="418" rx="250" ry="12" fill="rgba(0,0,0,.12)"/>

    <g filter="url(#rearShadow)">
        {{-- Roof arc --}}
        <path d="M 180 92 Q 300 42 420 92 L 438 170 L 162 170 Z"
              fill="url(#rearBody)" stroke="#8a8a8f" stroke-width="1.5"/>

        {{-- Rear window --}}
        <path d="M 195 96 Q 300 58 405 96 L 422 158 L 178 158 Z"
              fill="url(#rearGlass)" stroke="#3a6090" stroke-width="1.3" stroke-linejoin="round"/>
        {{-- Rear window reflection --}}
        <path d="M 225 92 Q 300 72 375 92 L 382 130 Q 300 116 218 130 Z" fill="#fff" opacity=".1"/>
        {{-- Rear wiper --}}
        <path d="M 300 100 Q 276 135 345 140" stroke="#2a2a2e" stroke-width="1.5" fill="none" stroke-linecap="round"/>
        <circle cx="300" cy="100" r="3.5" fill="#1a1a1e"/>
        {{-- Heating lines --}}
        <g stroke="#5580a8" stroke-width=".4" opacity=".3">
            <line x1="210" y1="112" x2="390" y2="112"/>
            <line x1="205" y1="120" x2="395" y2="120"/>
            <line x1="200" y1="128" x2="400" y2="128"/>
            <line x1="195" y1="136" x2="405" y2="136"/>
            <line x1="190" y1="144" x2="410" y2="144"/>
        </g>

        {{-- Body main --}}
        <path d="M 105 170 L 495 170 L 516 310 Q 518 368 492 388 L 108 388 Q 82 368 84 310 Z"
              fill="url(#rearBody)" stroke="#8a8a8f" stroke-width="1.5"/>

        {{-- Body shoulder highlight --}}
        <path d="M 115 180 L 485 180" stroke="#fff" stroke-width="1.8" opacity=".6"/>

        {{-- Trunk seam --}}
        <path d="M 105 220 Q 300 214 495 220" stroke="#a5a5aa" stroke-width="1.2" fill="none"/>
        {{-- Center trunk crease --}}
        <line x1="300" y1="180" x2="300" y2="330" stroke="#b5b5ba" stroke-width=".8" opacity=".5"/>

        {{-- Mirrors --}}
        <path d="M 110 155 Q 95 148 88 162 Q 90 180 112 180 Z" fill="#a8a8ad" stroke="#6a6a6f" stroke-width="1.1" opacity=".8" stroke-linejoin="round"/>
        <path d="M 490 155 Q 505 148 512 162 Q 510 180 488 180 Z" fill="#a8a8ad" stroke="#6a6a6f" stroke-width="1.1" opacity=".8" stroke-linejoin="round"/>

        {{-- Taillights (modern full-width LED bar style) --}}
        <path d="M 88 230 L 210 230 L 220 268 L 98 278 Q 86 274 84 258 Z"
              fill="url(#tailLightL)" stroke="#5a1010" stroke-width="1.2" stroke-linejoin="round"/>
        <path d="M 512 230 L 390 230 L 380 268 L 502 278 Q 514 274 516 258 Z"
              fill="url(#tailLightR)" stroke="#5a1010" stroke-width="1.2" stroke-linejoin="round"/>
        
        {{-- LED strips inside taillights --}}
        <path d="M 100 242 L 200 240" stroke="#ffc0c0" stroke-width="2" opacity=".8" stroke-linecap="round"/>
        <path d="M 100 254 L 205 252" stroke="#ffc0c0" stroke-width="1.2" opacity=".5" stroke-linecap="round"/>
        <path d="M 500 242 L 400 240" stroke="#ffc0c0" stroke-width="2" opacity=".8" stroke-linecap="round"/>
        <path d="M 500 254 L 395 252" stroke="#ffc0c0" stroke-width="1.2" opacity=".5" stroke-linecap="round"/>

        {{-- Center light bar (connecting taillights like modern Audi/Porsche) --}}
        <rect x="220" y="238" width="160" height="12" rx="3" fill="#2a2a2e" stroke="#4a4a4e" stroke-width=".8"/>
        <line x1="225" y1="244" x2="375" y2="244" stroke="#c42a2a" stroke-width="1.5" opacity=".6"/>

        {{-- Reverse/indicator lights --}}
        <rect x="235" y="330" width="45" height="12" rx="2.5" fill="#fff5cc" stroke="#a08530" stroke-width="1"/>
        <rect x="320" y="330" width="45" height="12" rx="2.5" fill="#ff8a8a" stroke="#8a2020" stroke-width="1"/>

        {{-- Brand badge --}}
        <rect x="276" y="282" width="48" height="16" rx="2.5" fill="#2a2a2e" stroke="#8a8a8f" stroke-width=".8"/>
        <text x="300" y="294" text-anchor="middle" font-family="Inter,system-ui,sans-serif" font-size="8" font-weight="800" fill="#b0b0b5" letter-spacing="1">CERTI</text>

        {{-- License plate --}}
        <rect x="245" y="303" width="110" height="28" rx="3.5" fill="#fff" stroke="#555" stroke-width="1.2"/>
        <rect x="248" y="305" width="14" height="24" rx="2" fill="#003399" opacity=".5"/>
        <text x="300" y="322" text-anchor="middle" font-family="monospace" font-size="14" fill="#222" font-weight="700">CC-2026</text>

        {{-- Bumper lines --}}
        <path d="M 100 345 L 500 345" stroke="#6a6a6e" stroke-width="1.2" opacity=".5"/>
        <path d="M 84 365 L 516 365" stroke="#6a6a6e" stroke-width=".8" opacity=".4"/>

        {{-- Exhaust (dual quad tips) --}}
        <g>
            <ellipse cx="155" cy="378" rx="16" ry="8" fill="#2e2e32" stroke="#1a1a1e" stroke-width="1"/>
            <ellipse cx="155" cy="377" rx="12" ry="5" fill="#1a1a1e"/>
            <ellipse cx="180" cy="378" rx="16" ry="8" fill="#2e2e32" stroke="#1a1a1e" stroke-width="1"/>
            <ellipse cx="180" cy="377" rx="12" ry="5" fill="#1a1a1e"/>
        </g>
        <g>
            <ellipse cx="420" cy="378" rx="16" ry="8" fill="#2e2e32" stroke="#1a1a1e" stroke-width="1"/>
            <ellipse cx="420" cy="377" rx="12" ry="5" fill="#1a1a1e"/>
            <ellipse cx="445" cy="378" rx="16" ry="8" fill="#2e2e32" stroke="#1a1a1e" stroke-width="1"/>
            <ellipse cx="445" cy="377" rx="12" ry="5" fill="#1a1a1e"/>
        </g>

        {{-- Rear diffuser --}}
        <path d="M 145 368 L 455 368" stroke="#3a3a3e" stroke-width="1.5"/>
        <line x1="200" y1="368" x2="200" y2="388" stroke="#3a3a3e" stroke-width=".8" opacity=".5"/>
        <line x1="300" y1="368" x2="300" y2="388" stroke="#3a3a3e" stroke-width=".8" opacity=".5"/>
        <line x1="400" y1="368" x2="400" y2="388" stroke="#3a3a3e" stroke-width=".8" opacity=".5"/>

        {{-- Wheels --}}
        <g>
            <circle cx="115" cy="378" r="26" fill="#1a1a1e" stroke="#000" stroke-width="1.5"/>
            <circle cx="115" cy="378" r="20" fill="#333"/>
            <circle cx="115" cy="378" r="16" fill="#777"/>
            <circle cx="115" cy="378" r="12" fill="#555"/>
            <circle cx="115" cy="378" r="5" fill="#2a2a2e"/>
            <g stroke="#444" stroke-width="3" stroke-linecap="round" fill="none">
                <line x1="115" y1="362" x2="115" y2="394"/>
                <line x1="130" y1="370" x2="100" y2="386"/>
                <line x1="130" y1="386" x2="100" y2="370"/>
            </g>
        </g>
        <g>
            <circle cx="485" cy="378" r="26" fill="#1a1a1e" stroke="#000" stroke-width="1.5"/>
            <circle cx="485" cy="378" r="20" fill="#333"/>
            <circle cx="485" cy="378" r="16" fill="#777"/>
            <circle cx="485" cy="378" r="12" fill="#555"/>
            <circle cx="485" cy="378" r="5" fill="#2a2a2e"/>
            <g stroke="#444" stroke-width="3" stroke-linecap="round" fill="none">
                <line x1="485" y1="362" x2="485" y2="394"/>
                <line x1="500" y1="370" x2="470" y2="386"/>
                <line x1="500" y1="386" x2="470" y2="370"/>
            </g>
        </g>
    </g>

    <g font-family="Inter,system-ui,sans-serif" font-weight="700" letter-spacing="2">
        <text x="300" y="22" text-anchor="middle" font-size="9" fill="#888">WIDOK Z TYŁU</text>
    </g>
</svg>
