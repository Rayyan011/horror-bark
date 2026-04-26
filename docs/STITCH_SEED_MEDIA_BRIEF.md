# Horror Bark Seed Media Brief For Stitch

## Goal
Generate image-first seed content for the Horror Bark booking platform.

This request is for content data and promotional imagery, not UI screens.

Only generate data for:

- Hotels
- Hotel rooms
- Theme park rides
- Theme park games
- Beach events

Do not generate ferry or ferry ticket data. Ferry records were intentionally excluded because that model is not image-driven.

## Source Model Constraints

Use these model rules as the source of truth.

### Hotels
Table: `hotels`

Required or expected fields:

- `user_id`
- `name`
- `location`
- `description`
- `latitude` nullable
- `longitude` nullable
- `map_x` nullable
- `map_y` nullable
- `images` as a list of image paths

Notes:

- Hotels belong to an owner user.
- Hotels can have multiple images, but one strong primary image per hotel is enough for this batch.

### Rooms
Table: `rooms`

Required or expected fields:

- `hotel_id`
- `room_number`
- `price`
- `status`
- `max_occupancy`
- `amenities`
- `images`
- `description`

Notes:

- Rooms belong to hotels.
- The app model and Filament resource treat `amenities` and `images` like lists.
- Important implementation note: the original `rooms.images` migration is a `string`, but the model and admin form behave like it is a JSON list. For this brief, treat room images as a list and serialize them during import if needed.
- Use `status: available` for all generated rooms unless there is a strong reason otherwise.

### Rides
Table: `rides`

Required or expected fields:

- `user_id`
- `island_id`
- `name`
- `description`
- `price`
- `latitude` nullable
- `longitude` nullable
- `map_x` nullable
- `map_y` nullable
- `images`
- `max_capacity`
- `max_booking_quantity`

Notes:

- Rides belong to a user owner.
- Rides must be attached to an island of type `Horror-Island`.

### Games
Table: `games`

Required or expected fields:

- `user_id`
- `island_id`
- `name`
- `description`
- `price`
- `latitude` nullable
- `longitude` nullable
- `map_x` nullable
- `map_y` nullable
- `images`
- `max_capacity`
- `max_booking_quantity`

Notes:

- Games belong to a user owner.
- Games must be attached to an island of type `Horror-Island`.

### Beach Events
Table: `beach_events`

Required or expected fields:

- `user_id`
- `island_id`
- `name`
- `description`
- `event_date`
- `price`
- `latitude` nullable
- `longitude` nullable
- `map_x` nullable
- `map_y` nullable
- `images`
- `max_capacity`
- `max_booking_quantity`

Notes:

- Beach events belong to a user owner.
- Beach events must be attached to an island of type `Picnic-Island`.

## Existing Reference Data To Use

Use these symbolic references instead of raw numeric IDs. They can be mapped to IDs during seeding.

### Owners

- `evelyn.thorne@horrorbark.test` for hotels
- `silas.blackwood@horrorbark.test` for rides
- `jasper.crowe@horrorbark.test` for games
- `ophelia.vale@horrorbark.test` for beach events

### Island References

Horror island options:

- `Manor Ward`
- `Shadow Park`
- `Lantern Hollow`
- `Blackwater Approach`

Picnic island options:

- `Pale Moon Strand`
- `Saltveil Beach`
- `Coven Quay`
- `Blackwater Shore`

## Deliverable Size

Generate at minimum:

- 3 hotels
- 3 rooms per hotel, 9 rooms total
- 3 rides
- 3 games
- 2 beach events

That is a minimum of 20 image-backed records total.

## What We Want Most From Stitch

The main value is image generation.

For every hotel, room, ride, game, and beach event:

- generate 1 high-quality primary image
- keep a consistent Horror Bark visual world
- make images cinematic, premium, eerie, and marketable
- avoid gore, dismemberment, or anything too extreme for a public booking site
- do not add text overlays, captions, logos, or UI chrome
- do not produce collages
- do not produce posters unless the prompt explicitly asks for an event scene
- prefer atmospheric realism over cartoon styling

Visual direction:

- gothic luxury resort
- moonlit coastal horror
- rich blacks, deep teal, ember gold, bruised violet, fog gray
- polished hospitality with theatrical dread
- expensive, immersive, and slightly surreal

## File And Path Expectations

Prefer one image path per record using these storage-style directories:

- hotels: `hotels/gallery/<slug>-01.png`
- rooms: `rooms/gallery/<slug>-01.png`
- rides: `rides/gallery/<slug>-01.png`
- games: `games/gallery/<slug>-01.png`
- beach events: `beach-events/gallery/<slug>-01.png`

Put the generated image path inside the model `images` list, even if there is only one image.

## Preferred Output Format

Return structured seed data as JSON or Markdown tables with these top-level groups:

- `hotels`
- `rooms`
- `rides`
- `games`
- `beach_events`

Every record should include:

- a stable `slug`
- all required content fields
- symbolic relationship references such as `owner_email`, `hotel_slug`, or `island_name`
- `images` with the generated primary image path
- a short `image_prompt` explaining the intended image

## Concrete Dataset To Generate

Use the following set. Keep the names and relationships fixed unless there is a strong quality reason to improve wording slightly.

### Hotels

| slug | owner_email | name | location | description | map_x | map_y | image_prompt |
| --- | --- | --- | --- | --- | ---: | ---: | --- |
| `the-shining-manor` | `evelyn.thorne@horrorbark.test` | The Shining Manor | Manor Ward · Keeper's Gate | The flagship estate of Horror Bark, defined by cold stone halls, velvet corridors, candlelit lounges, and elevated gothic hospitality. | 29.00 | 20.00 | Exterior twilight view of an upscale gothic manor hotel with glowing windows, wet stone, moonlight, velvet-purple accents, premium travel photography style. |
| `velvet-wake-house` | `evelyn.thorne@horrorbark.test` | Velvet Wake House | Blackwater Approach · Night Tide Dock | A harbor-facing boutique stay for late arrivals, with black-water views, lantern-lit balconies, and discreet luxury. | 21.00 | 70.00 | Elegant coastal harbor hotel at night, black tide below, lantern reflections, misty docks, teal and gold lighting, cinematic realism. |
| `coldstone-chambers` | `evelyn.thorne@horrorbark.test` | Coldstone Chambers | Lantern Hollow · Moonfall Steps | Quiet chapel-quarter accommodations with cedar smoke, stone arches, intimate lounges, and a hushed old-world mood. | 68.00 | 30.00 | Refined stone guesthouse near a gothic chapel district, lantern glow, drifting fog, ember highlights, boutique hospitality photo. |

### Rooms

All room records should use:

- `status: available`
- `images` with one primary generated room image

| slug | hotel_slug | room_number | price | max_occupancy | amenities | description | image_prompt |
| --- | --- | --- | ---: | ---: | --- | --- | --- |
| `shining-north-tower-suite` | `the-shining-manor` | SM-101 · North Tower Suite | 780.00 | 2 | `["Moonlit bath","Velvet lounge","Private supper service"]` | A secluded tower suite above Keeper's Gate with moonlit stone, heavy drapery, and a formal dining nook. | Luxurious gothic suite interior, moonlight through tall arched windows, velvet chaise, polished stone bath, premium hotel photography. |
| `shining-velvet-gallery-room` | `the-shining-manor` | SM-204 · Velvet Gallery Room | 620.00 | 2 | `["Gallery breakfast","Marble washstand","Lantern service"]` | An intimate chamber overlooking the manor gallery, designed for elegant overnight stays with silver-service touches. | Boutique gothic hotel room with gallery overlook, deep violet textiles, marble vanity, warm lantern light, upscale interior photo. |
| `shining-midnight-conservatory` | `the-shining-manor` | SM-310 · Midnight Conservatory Suite | 840.00 | 3 | `["Glass conservatory nook","Private tea service","Night concierge"]` | A premium suite with a glass-roof sitting room, rare plants, and a midnight tea setting under the moon. | Dramatic luxury suite with indoor conservatory corner, moonlit glass ceiling, exotic dark greenery, rich velvet seating, cinematic realism. |
| `wake-harbor-view-chamber` | `velvet-wake-house` | VW-110 · Harbor View Chamber | 540.00 | 3 | `["Dockside breakfast","Storm glass bar","Night tide balcony"]` | A harbor-facing chamber with panoramic dock views and a compact private bar for late-night arrivals. | Stylish coastal horror hotel room overlooking black water and docks, teal accents, foggy harbor lights, realistic travel photo. |
| `wake-bell-tower-room` | `velvet-wake-house` | VW-203 · Bell Tower Room | 585.00 | 2 | `["Private writing desk","Sea-facing balcony","Late ferry valet"]` | A high room above the harbor bells, with a writing desk, balcony seating, and a view across the black tide. | Tall narrow luxury room in a harbor bell tower, balcony doors open to misty sea, antique desk, moody teal-gold lighting. |
| `wake-tidecaller-suite` | `velvet-wake-house` | VW-305 · Tidecaller Suite | 690.00 | 4 | `["Corner lounge","Harbor soaking tub","Private arrival service"]` | A larger corner suite designed for small groups, with wraparound views of the dock lanterns and tide. | Premium harbor suite with panoramic windows, black-water view, freestanding tub, layered textiles, cinematic boutique hotel photo. |
| `coldstone-lantern-cellar` | `coldstone-chambers` | CC-008 · Lantern Cellar | 460.00 | 2 | `["Cedar stove","Lantern alcove","Stone bath"]` | A low-lit cellar suite with rough stone textures, cedar warmth, and a tucked-away private bathing area. | Atmospheric stone cellar suite, warm lantern alcoves, carved bath, ember glow, intimate luxury interior photography. |
| `coldstone-moonfall-loft` | `coldstone-chambers` | CC-302 · Moonfall Loft | 510.00 | 4 | `["Loft sitting room","Night watch service","Gathering table"]` | A lofted family room near Lantern Hollow, balancing cozy occupancy with the property's dark ceremonial character. | Lofted gothic family suite with exposed beams, long gathering table, warm amber lanterns, realistic boutique lodging photo. |
| `coldstone-chapel-eaves` | `coldstone-chambers` | CC-214 · Chapel Eaves Room | 550.00 | 2 | `["Window prayer nook","Cedar wardrobe","Late cocoa tray"]` | A serene upper-floor room with chapel roofline views and a calmer, more reflective take on the Horror Bark mood. | Quiet gothic guest room beneath chapel eaves, arched window seat, cedar wardrobe, soft ember light, premium hospitality image. |

### Rides

All rides must reference a `Horror-Island` island.

| slug | owner_email | island_name | name | price | max_capacity | max_booking_quantity | map_x | map_y | description | image_prompt |
| --- | --- | --- | --- | ---: | ---: | ---: | ---: | ---: | --- | --- |
| `widows-descent` | `silas.blackwood@horrorbark.test` | Shadow Park | Widow's Descent | 190.00 | 28 | 4 | 38.00 | 54.00 | A towering plunge ride threaded through torn velvet, black iron, and tolling bells. | Signature horror theme park drop tower at night, black iron structure, violet lighting, fog, premium amusement destination photography. |
| `velvet-spiral` | `silas.blackwood@horrorbark.test` | Manor Ward | Velvet Spiral | 165.00 | 22 | 4 | 37.00 | 27.00 | A polished manor-side coaster that coils through violet lamps and ceremonial arches. | Elegant gothic steel coaster near manor architecture, moonlit sky, violet lantern glow, polished rails, cinematic realism. |
| `the-ash-procession` | `silas.blackwood@horrorbark.test` | Lantern Hollow | The Ash Procession | 150.00 | 18 | 3 | 61.00 | 33.00 | A solemn dark ride passing through smoke, lantern arches, and chapel-like set pieces. | Immersive dark ride exterior and queue area with lantern arches, ash haze, cedar textures, premium theme park promotional photo. |

### Games

All games must reference a `Horror-Island` island.

| slug | owner_email | island_name | name | price | max_capacity | max_booking_quantity | map_x | map_y | description | image_prompt |
| --- | --- | --- | --- | ---: | ---: | ---: | ---: | ---: | --- | --- |
| `lantern-guess` | `jasper.crowe@horrorbark.test` | Shadow Park | Lantern Guess | 45.00 | 30 | 6 | 44.00 | 60.00 | A timing-and-observation game where guests choose the correct warded lantern before the flame drops. | Premium carnival skill game stall with glowing lanterns, black and gold details, moody fog, cinematic theme park photography. |
| `the-silent-wheel` | `jasper.crowe@horrorbark.test` | Manor Ward | The Silent Wheel | 60.00 | 24 | 4 | 32.00 | 31.00 | A velvet-draped wheel-of-fortune attraction that feels ceremonial rather than playful. | Gothic fortune wheel game with velvet drapery, polished brass, candlelit atmosphere, upscale eerie carnival image. |
| `coven-toss` | `jasper.crowe@horrorbark.test` | Lantern Hollow | Coven Toss | 40.00 | 26 | 5 | 58.00 | 39.00 | A ring-toss midway game built from ashwood posts, bone-white rings, and occult carnival styling. | Refined horror midway toss game, ashwood booth, lantern light, amber accents, premium destination marketing photo. |

### Beach Events

All beach events must reference a `Picnic-Island` island.

Use explicit future dates.

| slug | owner_email | island_name | name | event_date | price | max_capacity | max_booking_quantity | map_x | map_y | description | image_prompt |
| --- | --- | --- | --- | --- | ---: | ---: | ---: | ---: | ---: | --- | --- |
| `moonlight-vigil` | `ophelia.vale@horrorbark.test` | Pale Moon Strand | Moonlight Vigil | `2026-04-18` | 120.00 | 80 | 4 | 76.00 | 67.00 | An after-dark shoreline gathering of lanterns, strings, and whispered vows timed to the turning tide. | Moonlit luxury beach event with lanterns, dark shoreline, elegant seating, silver-blue surf, cinematic event photography. |
| `velvet-bonfire` | `ophelia.vale@horrorbark.test` | Saltveil Beach | Velvet Bonfire | `2026-04-25` | 135.00 | 60 | 4 | 57.00 | 16.00 | A black-sand ceremony built around a controlled bonfire, velvet seating, and salt-heavy sea air. | Upscale nocturnal beach bonfire event on dark sand, ember glow, velvet lounge seating, moody coastal realism. |

## Output Quality Rules

- Keep naming polished and consistent with the Horror Bark world.
- Descriptions should sound premium and immersive, not generic.
- Prices should feel like destination-experience pricing, not casual fairground pricing.
- Images should look like tourism-grade marketing visuals for a gothic luxury theme park resort.
- Keep all content safe for a public-facing booking platform.

## Final Instruction To Stitch

Produce the minimum batch above as seed-ready structured content with one primary generated image per record.

Focus on image quality first.

If a field is not essential for visual generation, still return it in the structured data so the records can be imported later with minimal manual cleanup.
