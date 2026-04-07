<?php

namespace App\Http\Controllers;

use App\Support\HorrorGeneratedMediaCatalog;
use Illuminate\Http\Response;
use Illuminate\Support\HtmlString;

class GeneratedMediaController extends Controller
{
    public function __invoke(string $collection, string $slug): Response
    {
        $entry = HorrorGeneratedMediaCatalog::entry($collection, $slug);

        abort_unless($entry, 404);

        $palette = $this->palette($entry['palette'] ?? 'violet');

        return response()
            ->view('generated-media.scene', [
                'entry' => $entry,
                'palette' => $palette,
                'sceneMarkup' => new HtmlString($this->sceneMarkup($entry['scene'] ?? 'poster', $entry, $palette)),
            ])
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'public, max-age=604800');
    }

    private function palette(string $name): array
    {
        return match ($name) {
            'teal' => [
                'skyTop' => '#09131a',
                'skyBottom' => '#102536',
                'fog' => '#7ab7c3',
                'moon' => '#d5edf2',
                'ground' => '#10181b',
                'accent' => '#68d1d0',
                'accentSoft' => '#2d6971',
                'window' => '#dff8f9',
                'text' => '#f0fafc',
                'subtext' => '#acd1d8',
            ],
            'ember' => [
                'skyTop' => '#140d10',
                'skyBottom' => '#2b171d',
                'fog' => '#c2745a',
                'moon' => '#f1d1bf',
                'ground' => '#1a1414',
                'accent' => '#d96d45',
                'accentSoft' => '#6f3425',
                'window' => '#ffd3aa',
                'text' => '#fff1e8',
                'subtext' => '#d6b5a5',
            ],
            'amber' => [
                'skyTop' => '#161014',
                'skyBottom' => '#302125',
                'fog' => '#d2a362',
                'moon' => '#f6e0b6',
                'ground' => '#1d1716',
                'accent' => '#e0a24d',
                'accentSoft' => '#76521f',
                'window' => '#ffe0a8',
                'text' => '#fff6ea',
                'subtext' => '#d9c3a0',
            ],
            'gold' => [
                'skyTop' => '#141018',
                'skyBottom' => '#281f31',
                'fog' => '#e5b45d',
                'moon' => '#fde8b2',
                'ground' => '#181418',
                'accent' => '#f0b24c',
                'accentSoft' => '#7a531e',
                'window' => '#fff0c7',
                'text' => '#fff8ea',
                'subtext' => '#dbc9a7',
            ],
            default => [
                'skyTop' => '#0c0b16',
                'skyBottom' => '#1d1530',
                'fog' => '#9a85bf',
                'moon' => '#f0ebff',
                'ground' => '#120f1a',
                'accent' => '#9c6bff',
                'accentSoft' => '#4c2d84',
                'window' => '#f5d7ff',
                'text' => '#faf6ff',
                'subtext' => '#c6b9dd',
            ],
        };
    }

    private function sceneMarkup(string $scene, array $entry, array $palette): string
    {
        return match ($scene) {
            'manor' => $this->manorScene($palette),
            'harbor' => $this->harborScene($palette),
            'chapel' => $this->chapelScene($palette),
            'suite' => $this->suiteScene($palette),
            'gallery' => $this->galleryScene($palette),
            'harbor-room' => $this->harborRoomScene($palette),
            'tower-room' => $this->towerRoomScene($palette),
            'cellar' => $this->cellarScene($palette),
            'loft' => $this->loftScene($palette),
            'coaster' => $this->coasterScene($palette),
            'drop-tower' => $this->dropTowerScene($palette),
            'spiral' => $this->spiralScene($palette),
            'procession' => $this->processionScene($palette),
            'game-stall' => $this->gameStallScene($palette),
            'wheel-game' => $this->wheelGameScene($palette),
            'fortune-table' => $this->fortuneTableScene($palette),
            'bonfire' => $this->bonfireScene($palette),
            'vigil' => $this->vigilScene($palette),
            'shoreline' => $this->shorelineScene($palette),
            default => $this->posterScene($palette, $entry),
        };
    }

    private function manorScene(array $palette): string
    {
        return <<<SVG
<g>
  <path d="M180 645L360 462H1240L1410 645V720H180V645Z" fill="#18131f"/>
  <rect x="312" y="322" width="620" height="330" rx="16" fill="#221b2f"/>
  <rect x="930" y="370" width="180" height="282" rx="14" fill="#20192c"/>
  <rect x="220" y="362" width="120" height="290" rx="12" fill="#1d1528"/>
  <rect x="1084" y="332" width="110" height="320" rx="12" fill="#1d1528"/>
  <path d="M272 362L280 250H338L344 362H272Z" fill="#18121e"/>
  <path d="M1110 332L1118 220H1180L1188 332H1110Z" fill="#18121e"/>
  <path d="M530 322L622 238L712 322H530Z" fill="{$palette['accentSoft']}"/>
  <path d="M760 322L852 248L940 322H760Z" fill="{$palette['accentSoft']}"/>
  <rect x="586" y="476" width="92" height="176" rx="48" fill="#15101a"/>
  <rect x="428" y="406" width="68" height="92" rx="10" fill="{$palette['window']}" opacity="0.82"/>
  <rect x="756" y="406" width="68" height="92" rx="10" fill="{$palette['window']}" opacity="0.82"/>
  <rect x="970" y="432" width="58" height="82" rx="10" fill="{$palette['window']}" opacity="0.78"/>
  <rect x="250" y="432" width="52" height="82" rx="10" fill="{$palette['window']}" opacity="0.75"/>
  <path d="M0 720H1600V900H0V720Z" fill="#0f0b12"/>
</g>
SVG;
    }

    private function harborScene(array $palette): string
    {
        return <<<SVG
<g>
  <rect x="0" y="620" width="1600" height="280" fill="#0b1115"/>
  <path d="M0 690C140 666 260 702 402 690C538 678 708 628 862 646C1034 666 1196 742 1600 680V900H0V690Z" fill="#10202a"/>
  <rect x="170" y="430" width="360" height="190" rx="18" fill="#1c252c"/>
  <rect x="250" y="350" width="200" height="92" rx="14" fill="#202b34"/>
  <path d="M610 620H1420L1280 700H480L610 620Z" fill="#1b1417"/>
  <rect x="560" y="608" width="18" height="140" fill="#291c17"/>
  <rect x="772" y="588" width="18" height="160" fill="#291c17"/>
  <rect x="984" y="602" width="18" height="146" fill="#291c17"/>
  <rect x="244" y="470" width="70" height="94" rx="10" fill="{$palette['window']}" opacity="0.8"/>
  <rect x="350" y="470" width="70" height="94" rx="10" fill="{$palette['window']}" opacity="0.7"/>
  <path d="M1220 424L1282 330L1342 424H1220Z" fill="{$palette['accent']}"/>
  <rect x="1270" y="424" width="22" height="190" fill="#25181b"/>
  <circle cx="1281" cy="440" r="18" fill="{$palette['window']}" opacity="0.86"/>
  <ellipse cx="1210" cy="772" rx="250" ry="28" fill="{$palette['accent']}" opacity="0.16"/>
  <ellipse cx="892" cy="798" rx="210" ry="24" fill="{$palette['accent']}" opacity="0.12"/>
</g>
SVG;
    }

    private function chapelScene(array $palette): string
    {
        return <<<SVG
<g>
  <path d="M200 720L510 390H980L1180 560L1308 720H200Z" fill="#171117"/>
  <rect x="560" y="270" width="240" height="360" rx="20" fill="#231821"/>
  <path d="M540 352L680 188L822 352H540Z" fill="{$palette['accentSoft']}"/>
  <rect x="662" y="322" width="36" height="126" rx="18" fill="#181117"/>
  <rect x="618" y="364" width="124" height="30" rx="15" fill="#181117"/>
  <rect x="340" y="468" width="90" height="122" rx="12" fill="#1f171b"/>
  <rect x="950" y="468" width="90" height="122" rx="12" fill="#1f171b"/>
  <rect x="628" y="482" width="104" height="148" rx="52" fill="#140f12"/>
  <rect x="364" y="494" width="44" height="62" rx="10" fill="{$palette['window']}" opacity="0.72"/>
  <rect x="972" y="494" width="44" height="62" rx="10" fill="{$palette['window']}" opacity="0.72"/>
  <path d="M0 720H1600V900H0V720Z" fill="#100c0f"/>
</g>
SVG;
    }

    private function suiteScene(array $palette): string
    {
        return <<<SVG
<g>
  <rect x="0" y="0" width="1600" height="900" fill="#1a1220"/>
  <rect x="104" y="156" width="460" height="430" rx="24" fill="#251a31"/>
  <rect x="964" y="110" width="416" height="480" rx="24" fill="#1b1527"/>
  <circle cx="1160" cy="280" r="86" fill="{$palette['moon']}" opacity="0.84"/>
  <rect x="242" y="514" width="536" height="126" rx="18" fill="#4b233f"/>
  <rect x="268" y="464" width="484" height="94" rx="14" fill="#6b2f5f"/>
  <rect x="276" y="558" width="468" height="92" rx="12" fill="#cfbdd8"/>
  <rect x="840" y="602" width="524" height="58" rx="14" fill="#231923"/>
  <rect x="888" y="522" width="172" height="104" rx="16" fill="#4d3444"/>
  <rect x="1098" y="504" width="174" height="126" rx="16" fill="#2e2533"/>
  <ellipse cx="540" cy="746" rx="390" ry="110" fill="{$palette['accent']}" opacity="0.18"/>
</g>
SVG;
    }

    private function galleryScene(array $palette): string
    {
        return <<<SVG
<g>
  <rect width="1600" height="900" fill="#17111d"/>
  <rect x="150" y="150" width="300" height="420" rx="18" fill="#22192c"/>
  <rect x="512" y="150" width="220" height="420" rx="18" fill="#20182a"/>
  <rect x="770" y="150" width="220" height="420" rx="18" fill="#1f1728"/>
  <rect x="1060" y="120" width="360" height="480" rx="18" fill="#2b2330"/>
  <rect x="260" y="630" width="1080" height="38" rx="19" fill="#23161b"/>
  <rect x="300" y="660" width="1000" height="26" rx="13" fill="#1a1116"/>
  <circle cx="1240" cy="250" r="94" fill="{$palette['moon']}" opacity="0.24"/>
  <rect x="1148" y="418" width="190" height="140" rx="20" fill="#4a3250"/>
  <rect x="1184" y="450" width="118" height="88" rx="14" fill="#d6c6dd"/>
  <ellipse cx="1180" cy="764" rx="420" ry="96" fill="{$palette['accent']}" opacity="0.12"/>
</g>
SVG;
    }

    private function harborRoomScene(array $palette): string
    {
        return <<<SVG
<g>
  <rect width="1600" height="900" fill="#131a20"/>
  <rect x="106" y="148" width="520" height="520" rx="24" fill="#1f2a31"/>
  <rect x="714" y="188" width="720" height="408" rx="26" fill="#16212a"/>
  <circle cx="1084" cy="304" r="88" fill="{$palette['moon']}" opacity="0.78"/>
  <path d="M738 484C878 442 990 526 1148 490C1260 466 1336 438 1434 470V614H738V484Z" fill="#0e1e27"/>
  <rect x="238" y="534" width="468" height="110" rx="18" fill="#24505b"/>
  <rect x="274" y="488" width="398" height="84" rx="15" fill="#316d79"/>
  <rect x="1040" y="622" width="230" height="72" rx="16" fill="#2a1f21"/>
  <ellipse cx="920" cy="760" rx="360" ry="98" fill="{$palette['accent']}" opacity="0.14"/>
</g>
SVG;
    }

    private function towerRoomScene(array $palette): string
    {
        return <<<SVG
<g>
  <rect width="1600" height="900" fill="#131620"/>
  <rect x="196" y="140" width="280" height="620" rx="40" fill="#201b29"/>
  <rect x="248" y="182" width="176" height="318" rx="84" fill="#2b2335"/>
  <circle cx="336" cy="318" r="74" fill="{$palette['moon']}" opacity="0.78"/>
  <rect x="548" y="258" width="844" height="370" rx="28" fill="#1c1822"/>
  <rect x="652" y="544" width="504" height="108" rx="18" fill="#314f62"/>
  <rect x="682" y="500" width="450" height="74" rx="16" fill="#4d748d"/>
  <ellipse cx="962" cy="748" rx="370" ry="92" fill="{$palette['accent']}" opacity="0.16"/>
</g>
SVG;
    }

    private function cellarScene(array $palette): string
    {
        return <<<SVG
<g>
  <rect width="1600" height="900" fill="#120d10"/>
  <path d="M110 720V420C110 320 190 250 290 250H1310C1410 250 1490 320 1490 420V720H110Z" fill="#251b1a"/>
  <path d="M260 720V474C260 392 326 326 408 326H550C632 326 698 392 698 474V720H260Z" fill="#1b1312"/>
  <path d="M902 720V474C902 392 968 326 1050 326H1192C1274 326 1340 392 1340 474V720H902Z" fill="#1b1312"/>
  <circle cx="480" cy="516" r="30" fill="{$palette['window']}" opacity="0.72"/>
  <circle cx="1122" cy="516" r="30" fill="{$palette['window']}" opacity="0.72"/>
  <rect x="612" y="572" width="378" height="118" rx="18" fill="#5d4033"/>
  <rect x="636" y="526" width="332" height="74" rx="14" fill="#7b5743"/>
  <ellipse cx="802" cy="770" rx="470" ry="98" fill="{$palette['accent']}" opacity="0.16"/>
</g>
SVG;
    }

    private function loftScene(array $palette): string
    {
        return <<<SVG
<g>
  <rect width="1600" height="900" fill="#161214"/>
  <path d="M120 720L804 176L1480 720H120Z" fill="#21191b"/>
  <path d="M264 720L804 290L1344 720H264Z" fill="#2d2323"/>
  <rect x="358" y="560" width="488" height="108" rx="18" fill="#7a5d48"/>
  <rect x="396" y="512" width="418" height="72" rx="14" fill="#9a7759"/>
  <rect x="936" y="496" width="210" height="150" rx="18" fill="#2b2427"/>
  <rect x="980" y="538" width="124" height="84" rx="12" fill="{$palette['window']}" opacity="0.72"/>
  <ellipse cx="804" cy="778" rx="420" ry="88" fill="{$palette['accent']}" opacity="0.12"/>
</g>
SVG;
    }

    private function coasterScene(array $palette): string
    {
        return <<<SVG
<g>
  <rect y="620" width="1600" height="280" fill="#100d14"/>
  <path d="M100 708C160 482 324 356 444 520C572 694 662 664 768 528C890 372 1014 330 1122 460C1224 584 1332 620 1504 408" stroke="{$palette['accent']}" stroke-width="26" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
  <path d="M160 720L238 560M320 720L412 468M514 720L598 574M724 720L792 544M972 720L1032 470M1184 720L1236 530M1378 720L1436 456" stroke="#241b2b" stroke-width="18" stroke-linecap="round"/>
  <circle cx="1368" cy="244" r="86" fill="{$palette['moon']}" opacity="0.22"/>
</g>
SVG;
    }

    private function dropTowerScene(array $palette): string
    {
        return <<<SVG
<g>
  <rect y="660" width="1600" height="240" fill="#0f0c12"/>
  <rect x="748" y="166" width="96" height="540" rx="48" fill="#231a29"/>
  <rect x="710" y="308" width="172" height="62" rx="18" fill="{$palette['accent']}"/>
  <path d="M668 224L796 104L924 224H668Z" fill="#35204f"/>
  <circle cx="796" cy="214" r="18" fill="{$palette['window']}" opacity="0.74"/>
  <ellipse cx="796" cy="768" rx="320" ry="78" fill="{$palette['accent']}" opacity="0.12"/>
</g>
SVG;
    }

    private function spiralScene(array $palette): string
    {
        return <<<SVG
<g>
  <rect y="652" width="1600" height="248" fill="#110d14"/>
  <path d="M356 658C560 418 790 288 1022 344C1236 392 1370 560 1402 658" stroke="{$palette['accent']}" stroke-width="28" stroke-linecap="round" fill="none"/>
  <path d="M478 658C626 514 760 440 908 456C1056 472 1158 568 1208 658" stroke="#f0e8ff" stroke-opacity="0.25" stroke-width="18" stroke-linecap="round" fill="none"/>
  <rect x="336" y="628" width="24" height="112" fill="#251a20"/>
  <rect x="1380" y="628" width="24" height="112" fill="#251a20"/>
</g>
SVG;
    }

    private function processionScene(array $palette): string
    {
        return <<<SVG
<g>
  <rect y="652" width="1600" height="248" fill="#110d10"/>
  <path d="M204 720L432 420H1168L1394 720H204Z" fill="#1d1616"/>
  <path d="M352 720L514 510H1084L1248 720H352Z" fill="#2a1f1f"/>
  <circle cx="528" cy="492" r="22" fill="{$palette['window']}" opacity="0.7"/>
  <circle cx="794" cy="420" r="22" fill="{$palette['window']}" opacity="0.7"/>
  <circle cx="1060" cy="492" r="22" fill="{$palette['window']}" opacity="0.7"/>
  <path d="M792 720V394" stroke="{$palette['accent']}" stroke-width="18" stroke-linecap="round"/>
</g>
SVG;
    }

    private function gameStallScene(array $palette): string
    {
        return <<<SVG
<g>
  <rect y="662" width="1600" height="238" fill="#120e10"/>
  <rect x="236" y="400" width="1128" height="248" rx="24" fill="#23171a"/>
  <path d="M224 430L438 302H1160L1376 430H224Z" fill="{$palette['accentSoft']}"/>
  <rect x="340" y="478" width="208" height="110" rx="16" fill="#3a2522"/>
  <rect x="694" y="478" width="208" height="110" rx="16" fill="#3a2522"/>
  <rect x="1048" y="478" width="208" height="110" rx="16" fill="#3a2522"/>
  <circle cx="444" cy="532" r="22" fill="{$palette['accent']}"/>
  <circle cx="798" cy="532" r="22" fill="{$palette['accent']}"/>
  <circle cx="1152" cy="532" r="22" fill="{$palette['accent']}"/>
</g>
SVG;
    }

    private function wheelGameScene(array $palette): string
    {
        return <<<SVG
<g>
  <rect y="670" width="1600" height="230" fill="#110d11"/>
  <circle cx="800" cy="446" r="182" fill="none" stroke="{$palette['accent']}" stroke-width="24"/>
  <circle cx="800" cy="446" r="34" fill="{$palette['window']}" opacity="0.82"/>
  <path d="M800 262V630M618 446H982M676 320L924 572M924 320L676 572" stroke="{$palette['accentSoft']}" stroke-width="14" stroke-linecap="round"/>
  <rect x="768" y="612" width="64" height="146" rx="24" fill="#241824"/>
</g>
SVG;
    }

    private function fortuneTableScene(array $palette): string
    {
        return <<<SVG
<g>
  <rect width="1600" height="900" fill="#140f14"/>
  <ellipse cx="800" cy="592" rx="506" ry="170" fill="#311b2f"/>
  <ellipse cx="800" cy="592" rx="438" ry="138" fill="#4a2547"/>
  <circle cx="800" cy="414" r="118" fill="{$palette['moon']}" opacity="0.22"/>
  <circle cx="800" cy="414" r="84" fill="{$palette['accent']}" opacity="0.46"/>
  <circle cx="602" cy="592" r="26" fill="{$palette['accent']}"/>
  <circle cx="998" cy="592" r="26" fill="{$palette['accent']}"/>
  <circle cx="800" cy="712" r="26" fill="{$palette['accent']}"/>
</g>
SVG;
    }

    private function bonfireScene(array $palette): string
    {
        return <<<SVG
<g>
  <rect y="660" width="1600" height="240" fill="#111012"/>
  <path d="M0 706C212 642 338 714 566 686C750 666 930 566 1146 612C1308 648 1438 730 1600 694V900H0V706Z" fill="#151f22"/>
  <path d="M698 670L746 742H854L904 670H698Z" fill="#3a261a"/>
  <path d="M800 520C866 578 874 628 800 676C726 628 734 578 800 520Z" fill="{$palette['accent']}"/>
  <path d="M800 566C846 606 850 634 800 664C750 634 754 606 800 566Z" fill="#ffd39a"/>
  <circle cx="548" cy="606" r="22" fill="{$palette['window']}" opacity="0.72"/>
  <circle cx="1038" cy="588" r="22" fill="{$palette['window']}" opacity="0.72"/>
</g>
SVG;
    }

    private function vigilScene(array $palette): string
    {
        return <<<SVG
<g>
  <rect y="680" width="1600" height="220" fill="#0f1316"/>
  <path d="M0 722C180 680 348 754 540 712C710 674 856 606 1046 632C1222 658 1370 734 1600 694V900H0V722Z" fill="#132127"/>
  <circle cx="1194" cy="244" r="96" fill="{$palette['moon']}" opacity="0.78"/>
  <rect x="444" y="612" width="20" height="76" rx="10" fill="#23191d"/>
  <rect x="784" y="576" width="20" height="112" rx="10" fill="#23191d"/>
  <rect x="1118" y="624" width="20" height="64" rx="10" fill="#23191d"/>
  <circle cx="454" cy="610" r="22" fill="{$palette['window']}" opacity="0.82"/>
  <circle cx="794" cy="574" r="24" fill="{$palette['window']}" opacity="0.82"/>
  <circle cx="1128" cy="622" r="20" fill="{$palette['window']}" opacity="0.82"/>
</g>
SVG;
    }

    private function shorelineScene(array $palette): string
    {
        return <<<SVG
<g>
  <rect y="652" width="1600" height="248" fill="#0c1216"/>
  <path d="M0 716C196 672 356 724 556 690C724 662 930 558 1134 606C1308 648 1454 736 1600 692V900H0V716Z" fill="#11242b"/>
  <path d="M0 758C164 736 346 784 510 764C698 740 868 666 1036 682C1260 704 1412 802 1600 770V900H0V758Z" fill="{$palette['accent']}" opacity="0.16"/>
  <circle cx="1264" cy="246" r="92" fill="{$palette['moon']}" opacity="0.72"/>
  <path d="M316 684L378 622L438 684H316Z" fill="#2c1a18"/>
  <path d="M960 668L1028 590L1094 668H960Z" fill="#2c1a18"/>
</g>
SVG;
    }

    private function posterScene(array $palette, array $entry): string
    {
        $accent = $palette['accent'];
        $soft = $palette['accentSoft'];

        return <<<SVG
<g>
  <circle cx="1190" cy="218" r="118" fill="{$palette['moon']}" opacity="0.18"/>
  <path d="M166 720L446 336H1160L1432 720H166Z" fill="{$soft}"/>
  <path d="M284 720L510 438H1090L1312 720H284Z" fill="{$accent}" opacity="0.22"/>
  <path d="M548 720L804 252L1062 720H548Z" fill="{$accent}" opacity="0.3"/>
</g>
SVG;
    }
}
