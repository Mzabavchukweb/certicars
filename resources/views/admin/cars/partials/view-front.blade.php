<svg viewBox="0 0 600 440" xmlns="http://www.w3.org/2000/svg" style="width:100%;max-width:520px;display:block;margin:0 auto;pointer-events:none">
    <defs>
        <linearGradient id="frontBody" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#f2f2f4"/>
            <stop offset="50%" stop-color="#d5d5d9"/>
            <stop offset="100%" stop-color="#b0b0b5"/>
        </linearGradient>
        <linearGradient id="frontGlass" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#8bb0d6"/>
            <stop offset="100%" stop-color="#5580a8"/>
        </linearGradient>
        <linearGradient id="frontBumper" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#d0d0d4"/>
            <stop offset="100%" stop-color="#a8a8ad"/>
        </linearGradient>
        <radialGradient id="frontLightL" cx=".4" cy=".5" r=".7">
            <stop offset="0%" stop-color="#ffffff"/>
            <stop offset="40%" stop-color="#fff8d0"/>
            <stop offset="100%" stop-color="#c4a838"/>
        </radialGradient>
        <radialGradient id="frontLightR" cx=".6" cy=".5" r=".7">
            <stop offset="0%" stop-color="#ffffff"/>
            <stop offset="40%" stop-color="#fff8d0"/>
            <stop offset="100%" stop-color="#c4a838"/>
        </radialGradient>
        <filter id="frontShadow" x="-10%" y="-5%" width="120%" height="115%">
            <feGaussianBlur in="SourceAlpha" stdDeviation="8"/>
            <feOffset dy="8"/>
            <feComponentTransfer><feFuncA type="linear" slope=".2"/></feComponentTransfer>
            <feMerge><feMergeNode/><feMergeNode in="SourceGraphic"/></feMerge>
        </filter>
    </defs>

    {{-- Ground shadow --}}
    <ellipse cx="300" cy="418" rx="250" ry="12" fill="rgba(0,0,0,.12)"/>

    <g filter="url(#frontShadow)">
        {{-- Roof arc --}}
        <path d="M 170 92 Q 300 42 430 92 L 450 170 L 150 170 Z"
              fill="url(#frontBody)" stroke="#8a8a8f" stroke-width="1.5"/>

        {{-- Windshield --}}
        <path d="M 185 96 Q 300 58 415 96 L 432 156 L 168 156 Z"
              fill="url(#frontGlass)" stroke="#3a6090" stroke-width="1.3" stroke-linejoin="round"/>
        {{-- Windshield reflection --}}
        <path d="M 220 90 Q 300 68 380 90 L 390 125 Q 300 108 210 125 Z" fill="#fff" opacity=".1"/>
        {{-- A-pillar line --}}
        <line x1="300" y1="65" x2="300" y2="156" stroke="#5580a8" stroke-width=".5" opacity=".5"/>

        {{-- Body main --}}
        <path d="M 105 170 L 495 170 L 516 310 Q 518 368 492 388 L 108 388 Q 82 368 84 310 Z"
              fill="url(#frontBody)" stroke="#8a8a8f" stroke-width="1.5"/>

        {{-- Body shoulder highlight --}}
        <path d="M 115 180 L 485 180" stroke="#fff" stroke-width="1.8" opacity=".6"/>
        {{-- Body crease line --}}
        <path d="M 110 210 L 490 210" stroke="#c5c5ca" stroke-width=".8" opacity=".5"/>

        {{-- Mirrors --}}
        <path d="M 105 155 Q 88 148 82 164 Q 84 182 105 180 Z" fill="#b0b0b5" stroke="#6a6a6f" stroke-width="1.2" stroke-linejoin="round"/>
        <path d="M 495 155 Q 512 148 518 164 Q 516 182 495 180 Z" fill="#b0b0b5" stroke="#6a6a6f" stroke-width="1.2" stroke-linejoin="round"/>
        {{-- Mirror glass --}}
        <ellipse cx="90" cy="166" rx="6" ry="8" fill="#5580a8" opacity=".5"/>
        <ellipse cx="510" cy="166" rx="6" ry="8" fill="#5580a8" opacity=".5"/>

        {{-- Headlights (modern LED angular shape) --}}
        <path d="M 90 228 Q 100 210 165 215 L 205 228 L 205 258 L 165 268 Q 100 262 90 250 Z"
              fill="url(#frontLightL)" stroke="#9a8028" stroke-width="1.2" stroke-linejoin="round"/>
        <path d="M 510 228 Q 500 210 435 215 L 395 228 L 395 258 L 435 268 Q 500 262 510 250 Z"
              fill="url(#frontLightR)" stroke="#9a8028" stroke-width="1.2" stroke-linejoin="round"/>
        
        {{-- LED DRL strips --}}
        <path d="M 100 232 L 195 234" stroke="#fff" stroke-width="2.5" opacity=".9" stroke-linecap="round"/>
        <path d="M 405 234 L 500 232" stroke="#fff" stroke-width="2.5" opacity=".9" stroke-linecap="round"/>
        {{-- Inner LED modules --}}
        <circle cx="130" cy="244" r="5" fill="#fff" opacity=".6"/>
        <circle cx="155" cy="246" r="4" fill="#fff" opacity=".5"/>
        <circle cx="470" cy="244" r="5" fill="#fff" opacity=".6"/>
        <circle cx="445" cy="246" r="4" fill="#fff" opacity=".5"/>

        {{-- Upper grille (wide, modern kidney/single frame style) --}}
        <path d="M 210 218 L 390 218 L 395 258 Q 300 262 205 258 Z"
              fill="#1a1a1e" stroke="#3a3a3e" stroke-width="1.2" stroke-linejoin="round"/>
        {{-- Grille mesh lines --}}
        <line x1="215" y1="228" x2="385" y2="228" stroke="#404045" stroke-width=".6"/>
        <line x1="213" y1="238" x2="387" y2="238" stroke="#404045" stroke-width=".6"/>
        <line x1="210" y1="248" x2="390" y2="248" stroke="#404045" stroke-width=".6"/>
        {{-- Grille vertical dividers --}}
        <line x1="260" y1="220" x2="258" y2="256" stroke="#404045" stroke-width=".5"/>
        <line x1="340" y1="220" x2="342" y2="256" stroke="#404045" stroke-width=".5"/>
        {{-- Brand badge --}}
        <circle cx="300" cy="238" r="8" fill="#2a2a2e" stroke="#8a8a8f" stroke-width="1"/>
        <circle cx="300" cy="238" r="4.5" fill="#8a8a8f"/>

        {{-- Lower intake/bumper --}}
        <path d="M 145 310 L 455 310 L 455 350 Q 455 360 445 360 L 155 360 Q 145 360 145 350 Z"
              fill="#1e1e22" stroke="#3a3a3e" stroke-width="1.2" rx="8"/>
        {{-- Intake mesh --}}
        <line x1="155" y1="322" x2="445" y2="322" stroke="#404045" stroke-width=".5"/>
        <line x1="155" y1="334" x2="445" y2="334" stroke="#404045" stroke-width=".5"/>
        <line x1="155" y1="346" x2="445" y2="346" stroke="#404045" stroke-width=".5"/>

        {{-- Fog lights / corner accents --}}
        <path d="M 100 310 Q 108 295 140 300 L 140 340 Q 108 335 100 325 Z" fill="#fff4b8" stroke="#a08530" stroke-width="1" opacity=".8"/>
        <path d="M 500 310 Q 492 295 460 300 L 460 340 Q 492 335 500 325 Z" fill="#fff4b8" stroke="#a08530" stroke-width="1" opacity=".8"/>
        {{-- LED strip in fog light --}}
        <path d="M 108 316 L 135 314" stroke="#fff" stroke-width="1.5" opacity=".7" stroke-linecap="round"/>
        <path d="M 492 316 L 465 314" stroke="#fff" stroke-width="1.5" opacity=".7" stroke-linecap="round"/>

        {{-- License plate --}}
        <rect x="255" y="362" width="90" height="22" rx="3" fill="#fff" stroke="#555" stroke-width="1"/>
        <rect x="258" y="364" width="12" height="18" rx="1.5" fill="#003399" opacity=".5"/>
        <text x="300" y="378" text-anchor="middle" font-family="monospace" font-size="11" fill="#222" font-weight="700">CC-2026</text>

        {{-- Wheels (modern alloy design) --}}
        <g>
            <circle cx="128" cy="378" r="26" fill="#1a1a1e" stroke="#000" stroke-width="1.5"/>
            <circle cx="128" cy="378" r="20" fill="#333"/>
            <circle cx="128" cy="378" r="16" fill="#777"/>
            <circle cx="128" cy="378" r="12" fill="#555"/>
            <circle cx="128" cy="378" r="5" fill="#2a2a2e"/>
            {{-- 5-spoke pattern --}}
            <g stroke="#444" stroke-width="3" stroke-linecap="round" fill="none">
                <line x1="128" y1="362" x2="128" y2="394"/>
                <line x1="143" y1="370" x2="113" y2="386"/>
                <line x1="143" y1="386" x2="113" y2="370"/>
            </g>
        </g>
        <g>
            <circle cx="472" cy="378" r="26" fill="#1a1a1e" stroke="#000" stroke-width="1.5"/>
            <circle cx="472" cy="378" r="20" fill="#333"/>
            <circle cx="472" cy="378" r="16" fill="#777"/>
            <circle cx="472" cy="378" r="12" fill="#555"/>
            <circle cx="472" cy="378" r="5" fill="#2a2a2e"/>
            <g stroke="#444" stroke-width="3" stroke-linecap="round" fill="none">
                <line x1="472" y1="362" x2="472" y2="394"/>
                <line x1="487" y1="370" x2="457" y2="386"/>
                <line x1="487" y1="386" x2="457" y2="370"/>
            </g>
        </g>

        {{-- Front splitter/lip --}}
        <path d="M 88 388 L 512 388" stroke="#4a4a4e" stroke-width="2"/>
        <path d="M 92 392 L 508 392" stroke="#6a6a6e" stroke-width="1" opacity=".5"/>
    </g>

    <g font-family="Inter,system-ui,sans-serif" font-weight="700" letter-spacing="2">
        <text x="300" y="22" text-anchor="middle" font-size="9" fill="#888">WIDOK Z PRZODU</text>
    </g>
</svg>
