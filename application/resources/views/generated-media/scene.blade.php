<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1600 900" fill="none" role="img" aria-labelledby="title desc">
    <title id="title">{{ $entry['title'] }}</title>
    <desc id="desc">{{ $entry['subtitle'] }}</desc>

    <defs>
        <linearGradient id="sky" x1="800" y1="0" x2="800" y2="900" gradientUnits="userSpaceOnUse">
            <stop stop-color="{{ $palette['skyTop'] }}" />
            <stop offset="1" stop-color="{{ $palette['skyBottom'] }}" />
        </linearGradient>
        <linearGradient id="captionFade" x1="800" y1="620" x2="800" y2="900" gradientUnits="userSpaceOnUse">
            <stop stop-color="#050609" stop-opacity="0" />
            <stop offset="1" stop-color="#050609" stop-opacity="0.92" />
        </linearGradient>
        <filter id="moonGlow" x="-50%" y="-50%" width="200%" height="200%">
            <feGaussianBlur stdDeviation="28" />
        </filter>
    </defs>

    <rect width="1600" height="900" fill="url(#sky)" />

    <ellipse cx="1224" cy="214" rx="150" ry="106" fill="{{ $palette['fog'] }}" fill-opacity="0.22" filter="url(#moonGlow)" />
    <circle cx="1224" cy="214" r="82" fill="{{ $palette['moon'] }}" fill-opacity="0.86" />

    <path d="M0 612C156 560 302 602 460 582C622 562 734 474 920 494C1076 510 1184 608 1338 606C1450 604 1514 570 1600 544V900H0V612Z" fill="{{ $palette['ground'] }}" />
    <ellipse cx="376" cy="246" rx="150" ry="52" fill="{{ $palette['fog'] }}" fill-opacity="0.09" />
    <ellipse cx="994" cy="330" rx="226" ry="64" fill="{{ $palette['fog'] }}" fill-opacity="0.08" />

    {!! $sceneMarkup !!}

    <ellipse cx="438" cy="786" rx="252" ry="52" fill="{{ $palette['fog'] }}" fill-opacity="0.08" />
    <ellipse cx="1232" cy="808" rx="232" ry="46" fill="{{ $palette['fog'] }}" fill-opacity="0.08" />

    <rect y="620" width="1600" height="280" fill="url(#captionFade)" />

    <text x="96" y="736" fill="{{ $palette['accent'] }}" font-family="Georgia, 'Times New Roman', serif" font-size="30" letter-spacing="10">HORROR BARK</text>
    <text x="96" y="802" fill="{{ $palette['text'] }}" font-family="Georgia, 'Times New Roman', serif" font-size="78" font-style="italic">{{ $entry['title'] }}</text>
    <text x="96" y="850" fill="{{ $palette['subtext'] }}" font-family="Georgia, 'Times New Roman', serif" font-size="30">{{ $entry['subtitle'] }}</text>
</svg>
