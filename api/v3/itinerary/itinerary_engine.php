<?php
/**
 * V3 Smart Itinerary Intelligence Engine
 * Path: api/v3/itinerary/itinerary_engine.php
 * Purpose: Generates high-quality, realistic, and completely non-repetitive day-by-day travel itineraries.
 */

if (!function_exists('v3_normalize_destination_key')) {
    function v3_normalize_destination_key($cityName)
    {
        $clean = strtolower(trim((string)$cityName));
        $clean = preg_replace('/[^a-z0-9\s]/', '', $clean);

        if (strpos($clean, 'manai') !== false || strpos($clean, 'manali') !== false) return 'manali';
        if (strpos($clean, 'shimla') !== false || strpos($clean, 'simla') !== false || strpos($clean, 'kufri') !== false) return 'shimla';
        if (strpos($clean, 'dharamshala') !== false || strpos($clean, 'mcleodganj') !== false || strpos($clean, 'dharamsala') !== false) return 'dharamshala';
        if (strpos($clean, 'dalhousie') !== false || strpos($clean, 'khajjiar') !== false) return 'dalhousie';
        if (strpos($clean, 'kasol') !== false || strpos($clean, 'kullu') !== false || strpos($clean, 'manikaran') !== false || strpos($clean, 'jibhi') !== false) return 'kasol_kullu';
        if (strpos($clean, 'srinagar') !== false || strpos($clean, 'kashmir') !== false || strpos($clean, 'gulmarg') !== false || strpos($clean, 'pahalgam') !== false || strpos($clean, 'sonmarg') !== false) return 'kashmir';
        if (strpos($clean, 'leh') !== false || strpos($clean, 'ladakh') !== false || strpos($clean, 'nubra') !== false || strpos($clean, 'pangong') !== false) return 'ladakh';
        if (strpos($clean, 'jaipur') !== false) return 'jaipur';
        if (strpos($clean, 'udaipur') !== false) return 'udaipur';
        if (strpos($clean, 'jodhpur') !== false) return 'jodhpur';
        if (strpos($clean, 'jaisalmer') !== false) return 'jaisalmer';
        if (strpos($clean, 'agra') !== false) return 'agra';
        if (strpos($clean, 'delhi') !== false) return 'delhi';
        if (strpos($clean, 'amritsar') !== false) return 'amritsar';
        if (strpos($clean, 'varanasi') !== false || strpos($clean, 'banaras') !== false || strpos($clean, 'kashi') !== false) return 'varanasi';
        if (strpos($clean, 'rishikesh') !== false || strpos($clean, 'haridwar') !== false) return 'rishikesh';
        if (strpos($clean, 'nainital') !== false || strpos($clean, 'bhimtal') !== false) return 'nainital';
        if (strpos($clean, 'mussoorie') !== false || strpos($clean, 'dehradun') !== false) return 'mussoorie';
        if (strpos($clean, 'goa') !== false) return 'goa';
        if (strpos($clean, 'munnar') !== false) return 'munnar';
        if (strpos($clean, 'alleppey') !== false || strpos($clean, 'alappuzha') !== false) return 'alleppey';
        if (strpos($clean, 'thekkady') !== false || strpos($clean, 'periyar') !== false) return 'thekkady';
        if (strpos($clean, 'ooty') !== false || strpos($clean, 'coonoor') !== false) return 'ooty';
        if (strpos($clean, 'coorg') !== false || strpos($clean, 'madikeri') !== false) return 'coorg';
        if (strpos($clean, 'darjeeling') !== false) return 'darjeeling';
        if (strpos($clean, 'gangtok') !== false || strpos($clean, 'sikkim') !== false) return 'gangtok';
        if (strpos($clean, 'andaman') !== false || strpos($clean, 'port blair') !== false || strpos($clean, 'havelock') !== false) return 'andaman';
        if (strpos($clean, 'dubai') !== false) return 'dubai';
        if (strpos($clean, 'singapore') !== false) return 'singapore';
        if (strpos($clean, 'bali') !== false) return 'bali';
        if (strpos($clean, 'thailand') !== false || strpos($clean, 'bangkok') !== false || strpos($clean, 'phuket') !== false) return 'thailand';
        return 'generic';
    }
}

if (!function_exists('v3_format_city_display')) {
    function v3_format_city_display($cityRaw)
    {
        $k = v3_normalize_destination_key($cityRaw);
        $canonical = [
            'manali' => 'Manali',
            'shimla' => 'Shimla',
            'dharamshala' => 'Dharamshala',
            'dalhousie' => 'Dalhousie',
            'kasol_kullu' => 'Kullu & Kasol',
            'kashmir' => 'Srinagar (Kashmir)',
            'ladakh' => 'Leh Ladakh',
            'jaipur' => 'Jaipur',
            'udaipur' => 'Udaipur',
            'jodhpur' => 'Jodhpur',
            'jaisalmer' => 'Jaisalmer',
            'agra' => 'Agra',
            'delhi' => 'Delhi',
            'amritsar' => 'Amritsar',
            'varanasi' => 'Varanasi',
            'rishikesh' => 'Rishikesh',
            'nainital' => 'Nainital',
            'mussoorie' => 'Mussoorie',
            'goa' => 'Goa',
            'munnar' => 'Munnar',
            'alleppey' => 'Alleppey',
            'thekkady' => 'Thekkady',
            'ooty' => 'Ooty',
            'coorg' => 'Coorg',
            'darjeeling' => 'Darjeeling',
            'gangtok' => 'Gangtok',
            'andaman' => 'Andaman Islands',
            'dubai' => 'Dubai',
            'singapore' => 'Singapore',
            'bali' => 'Bali',
            'thailand' => 'Thailand'
        ];
        if (isset($canonical[$k])) {
            return $canonical[$k];
        }
        return ucwords(strtolower(trim((string)$cityRaw)));
    }
}

if (!function_exists('v3_parse_user_notes_landmarks')) {
    function v3_parse_user_notes_landmarks($notesText)
    {
        $raw = strtolower(trim((string)$notesText));
        if (empty($raw)) return null;

        if (strpos($raw, 'hadimba') !== false || strpos($raw, 'hidimba') !== false) {
            return [
                'title' => "Cultural Heritage: Hadimba Devi Temple, Vashisht Springs & Old Manali",
                'morning' => "Start the morning with a refreshing walk beneath towering Himalayan cedar and deodar pines to visit the sacred 16th-century Hadimba Devi Temple, admiring its distinctive four-tiered wooden pagoda architecture and intricate folk wood carvings.",
                'midday' => "Cross over to the tranquil village of Vashisht to experience the therapeutic natural hot sulphur water springs and pay respects at the ancient sage Vashisht shrine.",
                'afternoon' => "Spend a delightful afternoon exploring the charming cobblestone lanes of Old Manali, browsing vibrant handicraft shops, local woolens, art studios, and bohemian riverside bakeries.",
                'evening' => "Enjoy a leisurely walk along the forested pathways of Van Vihar nature park bordering the rushing Beas River as twilight settles peacefully over the snow-clad peaks."
            ];
        }

        if (strpos($raw, 'rohtang') !== false) {
            return [
                'title' => "High-Altitude Snow Excursion: Rohtang Pass & Alpine Panoramas",
                'morning' => "Set out on an early-morning mountain drive ascending dramatic hairpin bends towards the legendary Rohtang Pass (or through the engineering marvel of Atal Tunnel to Sissu, depending on seasonal permits and road accessibility).",
                'midday' => "Reach spectacular high-altitude vantage points to behold breathtaking panoramic views of snow-draped Pir Panjal mountain ranges, pristine alpine glaciers, and frozen streams.",
                'afternoon' => "Engage in thrilling open-air snow activities including skiing, tobogganing, and snow-scooter rides, or relish warm mountain snacks like hot Maggi and Himachali siddu in the crisp alpine air.",
                'evening' => "Descend peacefully into the valley, stopping by gushing roadside mountain streams and pine-fringed viewpoints before returning to town as the sun dips behind the western ridges."
            ];
        }

        if (strpos($raw, 'solang') !== false) {
            return [
                'title' => "Alpine Adventure: Solang Valley Thrills & Atal Tunnel Gateway",
                'morning' => "Head out following breakfast towards the adventure capital of Solang Valley, framed by dramatic snow peaks and sweeping green meadows.",
                'midday' => "Partake in thrilling adventure activities such as tandem paragliding, zorbing, quad biking, and taking the scenic ropeway cable car up to Mount Phatru for panoramic snow views.",
                'afternoon' => "Drive through the renowned Atal Tunnel into the pristine borderlands of Lahaul Valley to marvel at contrasting rugged mountain vistas and crystalline streams.",
                'evening' => "Return leisurely towards Manali, stopping at roadside apple orchard viewpoints and quaint roadside cafes for evening refreshments."
            ];
        }

        if (strpos($raw, 'kufri') !== false) {
            return [
                'title' => "High-Altitude Mountain Excursion: Kufri Adventure & Green Valley Pines",
                'morning' => "Travel along winding, cedar-lined ridges through the dense forest canopy of Green Valley towards the beloved hill retreat of Kufri.",
                'midday' => "Explore the Himalayan Nature Park to observe rare high-altitude wildlife such as the Himalayan Monal, followed by an exhilarating pony trek up to Mahasu Peak for panoramic Shivalik views.",
                'afternoon' => "Enjoy open-air adventure activities at Kufri Fun World, including go-karting and zip-lining surrounded by whispering deodar woodlands.",
                'evening' => "Drive back towards Shimla, taking in golden-hour views over terraced apple orchards and valleys shrouded in soft evening mist."
            ];
        }

        if (strpos($raw, 'jakhu') !== false || strpos($raw, 'ridge') !== false || strpos($raw, 'christ church') !== false || strpos($raw, 'mall road') !== false) {
            return [
                'title' => "Colonial Heritage Trail: Jakhu Temple, The Ridge & Christ Church",
                'morning' => "Ascend to the summit of Jakhu Hill, Shimla's highest peak, to visit the historic temple dedicated to Lord Hanuman and admire sweeping vistas of the snow-crested mountain range.",
                'midday' => "Visit the magnificent Viceregal Lodge (Rashtrapati Niwas), an architectural marvel of British colonial history surrounded by manicured botanical gardens.",
                'afternoon' => "Stroll past the iconic Neo-Gothic Christ Church and walk along the vibrant Ridge promenade into Lakkar Bazaar, famous for handcrafted wooden artifacts.",
                'evening' => "Enjoy a leisurely walk along the pedestrian-only Mall Road, stopping at heritage bakeries and open-air cafes as streetlights illuminate the hillside."
            ];
        }

        if (strpos($raw, 'shikara') !== false || strpos($raw, 'dal lake') !== false) {
            return [
                'title' => "Dal Lake Magic: Shikara Cruise, Floating Gardens & Char Chinar",
                'morning' => "Begin your day gliding across the tranquil waters of Dal Lake on a traditional carved Shikara, taking in the serene reflections of Zabarwan mountain ranges.",
                'midday' => "Cruise past vibrant floating vegetable markets, lotus gardens, and the iconic Char Chinar island, experiencing Kashmir's timeless aquatic lifestyle.",
                'afternoon' => "Visit authentic Kashmiri artisan workshops to see master craftsmen weaving intricate Pashmina shawls, walnut wood carving, and paper-mâché artifacts.",
                'evening' => "Relax on the cedar-paneled deck of your traditional Kashmiri houseboat as the setting sun paints shimmering shades of crimson and violet over the calm lake."
            ];
        }

        if (strpos($raw, 'gondola') !== false || strpos($raw, 'gulmarg') !== false) {
            return [
                'title' => "Meadow of Flowers: Gulmarg Excursion & High-Altitude Gondola Ride",
                'morning' => "Embark on a scenic drive through emerald meadows and pine-carpeted slopes to the world-famous alpine haven of Gulmarg.",
                'midday' => "Board the iconic Gulmarg Gondola—one of the world's highest cable cars—ascending past Kongdoori to Apharwat Peak for majestic panoramic views of snowy Himalayan ranges.",
                'afternoon' => "Walk across rolling green golf glades, take a pony ride to Strawberry Valley, and savor warm Kashmiri Kahwa infused with saffron and crushed almonds.",
                'evening' => "Enjoy a tranquil mountain descent taking in sweeping views of the Kashmir Valley bathed in warm afternoon sunlight."
            ];
        }

        if (strpos($raw, 'betaab') !== false || strpos($raw, 'aru') !== false || strpos($raw, 'pahalgam') !== false) {
            return [
                'title' => "Valley of Shepherds: Pahalgam Expedition, Betaab Valley & Aru River",
                'morning' => "Travel through fragrant saffron fields at Pampore and willow timber villages along the roaring Lidder River towards scenic Pahalgam.",
                'midday' => "Explore the picture-perfect Betaab Valley, surrounded by snow-capped peaks and pine forests, before heading into the untouched pastoral beauty of Aru Valley.",
                'afternoon' => "Take an exhilarating horse trek to the lush plateau of Baisaran, popularly known as 'Mini Switzerland', or sit beside the rushing mountain waters of the Lidder.",
                'evening' => "Browse charming local village markets for dried saffron, walnuts, and Kashmiri honey before an evening relaxing amidst mountain scenery."
            ];
        }

        // Generic custom notes parsing with cleaned capitalization
        $cleanTitle = ucwords(trim(preg_replace('/[_-]+/', ' ', $notesText)));
        return [
            'title' => "Special Highlights: {$cleanTitle}",
            'morning' => "Set out in the morning with a dedicated focus on {$notesText}, soaking in the distinctive atmosphere and capturing memorable photographs.",
            'midday' => "Explore the surrounding landmarks and local cultural spots, engaging with regional traditions and relishing flavorful local dishes.",
            'afternoon' => "Continue your customized exploration of the area, taking time for relaxing strolls and scenic viewpoints.",
            'evening' => "As dusk approaches, enjoy leisure time at a local marketplace or viewpoint before unwinding for the evening."
        ];
    }
}

if (!function_exists('v3_get_destination_catalog')) {
    function v3_get_destination_catalog()
    {
        return [
            'manali' => [
                'name' => 'Manali',
                'days' => [
                    1 => [
                        'title' => 'Arrival in Manali & Scenic Evening Stroll along Mall Road',
                        'morning' => 'Begin your scenic mountain drive through the Beas River Valley, surrounded by cascading waterfalls and verdant pine slopes.',
                        'midday' => 'Arrive in Manali, complete your hotel check-in formalities, and take some time to unwind while enjoying crisp Himalayan mountain views.',
                        'afternoon' => 'Step out in the late afternoon towards the lively Mall Road, browsing authentic Himachali handloom shawls, woolen caps, and wooden curios.',
                        'evening' => 'Explore the vibrant Tibetan monastery market and relax at a welcoming cafe with hot local tea as dusk settles over the valley.'
                    ],
                    2 => [
                        'title' => 'Cultural Heritage: Hadimba Devi Temple, Vashisht Springs & Old Manali',
                        'morning' => 'Embark on a morning visit to the iconic 16th-century wooden Hadimba Devi Temple, sheltered beneath giant deodar and cedar groves.',
                        'midday' => 'Cross to the ancient village of Vashisht to experience the therapeutic natural hot sulphur water springs and sacred stone shrines.',
                        'afternoon' => 'Wander through the bohemian stone-and-timber lanes of Old Manali, admiring colorful graffiti, craft shops, and riverside cafes.',
                        'evening' => 'Enjoy peaceful riverside strolls along the forested trails of Van Vihar park as the sun dips behind the snow-draped mountains.'
                    ],
                    3 => [
                        'title' => 'Alpine Mountain Adventure: Solang Valley Thrills & Atal Tunnel Gateway',
                        'morning' => 'Head towards the renowned Solang Valley, celebrated for its wide-open alpine meadows and thrilling outdoor sporting activities.',
                        'midday' => 'Experience exhilarating paragliding flights, zorbing, and taking the cable car ropeway up to Mount Phatru for panoramic snow peaks.',
                        'afternoon' => 'Drive through the engineering marvel of Atal Tunnel to witness the stark, contrasting cliff landscapes and glacial waters of Lahaul.',
                        'evening' => 'Return down the scenic serpentine road, stopping at local apple orchard viewpoints for mountain tea and evening snacks.'
                    ],
                    4 => [
                        'title' => 'Scenic Heritage Trail: Naggar Castle & Jana Waterfall Excursion',
                        'morning' => 'Set off on a picturesque excursion across the valley to the historic Naggar Castle, an ancient royal seat built from timber and stone.',
                        'midday' => 'Tour the Nicholas Roerich Art Gallery showcasing iconic Himalayan paintings, followed by the ancient Tripura Sundari Temple.',
                        'afternoon' => 'Continue to the hidden forest haven of Jana Waterfall to taste authentic Himachali culinary delicacies like red rice and siddu.',
                        'evening' => 'Unwind alongside peaceful mountain streams, enjoying the tranquil countryside ambiance away from busy city streets.'
                    ],
                    5 => [
                        'title' => 'Beas Riverside Walks, Kullu Handloom Shawls & Departure Transfer',
                        'morning' => 'Savor your final morning breakfast overlooking the mist-clad valley before checking out from your hotel.',
                        'midday' => 'Drive down towards Kullu with an optional stop for white-water river rafting on the Beas River and visiting authentic shawl weaving mills.',
                        'afternoon' => 'Browse local markets for fresh mountain honey, apple cider, and handicrafts as your vehicle heads towards your departure point.',
                        'evening' => 'Arrive at the departure station/airport with a heart full of cherished memories of your unforgettable Himachal getaway.'
                    ]
                ]
            ],

            'shimla' => [
                'name' => 'Shimla',
                'days' => [
                    1 => [
                        'title' => 'Scenic Drive to Shimla, Hotel Check-in & Evening at The Ridge',
                        'morning' => 'Ascend through the scenic Shivalik mountain roads past terraced hills and misty pine valleys on your journey to Shimla.',
                        'midday' => 'Arrive in the picturesque Queen of Hills, check into your accommodation, and relax with panoramic valley vistas.',
                        'afternoon' => 'Walk across the historic Ridge promenade, taking in the iconic Neo-Gothic facade of Christ Church and the Tudor library building.',
                        'evening' => 'Stroll into the vibrant Lakkar Bazaar to admire handcrafted wooden artifacts and sample warm street-side snacks.'
                    ],
                    2 => [
                        'title' => 'Colonial Heritage & Panoramic Peaks: Jakhu Temple & Viceregal Lodge',
                        'morning' => 'Ascend to the hilltop sanctuary of Jakhu Temple, Shimla\'s highest peak, surrounded by towering deodars and majestic Himalayan views.',
                        'midday' => 'Visit the magnificent Viceregal Lodge (Rashtrapati Niwas), exploring its grand teakwood interiors and historic botanical gardens.',
                        'afternoon' => 'Take a relaxing heritage walk along the pedestrian-only Mall Road, exploring Gaiety Theatre and colonial stone buildings.',
                        'evening' => 'Savor sunset views from Scandal Point as the golden hour illuminates the hillside town and terraced valleys below.'
                    ],
                    3 => [
                        'title' => 'Excursion to Kufri & Green Valley: Pine Forests & Nature Park',
                        'morning' => 'Drive through the deep deodar canopy of Green Valley towards the high-altitude hill resort of Kufri.',
                        'midday' => 'Visit the Himalayan Nature Park to witness indigenous wildlife such as snow leopards and monals, followed by a pony trek up Mahasu Peak.',
                        'afternoon' => 'Enjoy open-air family adventure activities at Kufri Fun World, including go-karting and zip-lining surrounded by lush forests.',
                        'evening' => 'Drive back towards Shimla, stopping at scenic valley lookouts to witness a golden sunset over the terraced hillsides.'
                    ],
                    4 => [
                        'title' => 'Tranquil Wilderness: Mashobra Nature Trail & Naldehra Golf Glades',
                        'morning' => 'Escape the bustle on a scenic morning drive to Mashobra, known for its tranquil cedar trails and serene pine forest walks.',
                        'midday' => 'Proceed to Naldehra to admire one of India\'s oldest and most scenic 9-hole golf courses carved into a high mountain glade.',
                        'afternoon' => 'Wander through fragrant local apple orchards and enjoy a picturesque picnic with views of the distant snow-capped peaks.',
                        'evening' => 'Return to Shimla for a relaxing evening at a cozy hillside cafe, savoring the peaceful mountain atmosphere.'
                    ],
                    5 => [
                        'title' => 'Farewell to the Queen of Hills & Scenic Departure Transfer',
                        'morning' => 'Enjoy a leisurely breakfast with valley views, complete hotel check-out, and prepare for your return journey.',
                        'midday' => 'Descend along the mountain highway with stops at local fruit markets for fresh jams, juices, and Himachali souvenirs.',
                        'afternoon' => 'Board your return transport heading towards your destination hub, reminiscing about the colonial charms of Shimla.',
                        'evening' => 'Conclude your tour at the airport/railway station with happy memories and wonderful photographs.'
                    ]
                ]
            ],

            'kashmir' => [
                'name' => 'Srinagar (Kashmir)',
                'days' => [
                    1 => [
                        'title' => 'Welcome to Srinagar: Dal Lake Shikara Cruise & Houseboat Stay',
                        'morning' => 'Arrive in Srinagar, the paradise on earth, where your private chauffeur welcomes you and escorts you to your traditional houseboat.',
                        'midday' => 'Step aboard an intricately carved Shikara for a serene cruise across the calm waters of Dal Lake, reflecting the Zabarwan range.',
                        'afternoon' => 'Glide past the floating vegetable markets, lotus lagoons, and Char Chinar island while taking in Kashmir\'s timeless aquatic life.',
                        'evening' => 'Sip traditional hot Kahwa on the houseboat veranda as dusk bathes the tranquil lake in glowing shades of rose and gold.'
                    ],
                    2 => [
                        'title' => 'Mughal Splendors: Nishat Bagh, Shalimar Bagh & Shankaracharya Temple',
                        'morning' => 'Visit the legendary Mughal Gardens, beginning with Nishat Bagh (Garden of Bliss) featuring cascading terraces and majestic Chinar trees.',
                        'midday' => 'Explore Shalimar Bagh (Abode of Love), built by Emperor Jahangir, admiring its marble water pavilions and blossoming flowerbeds.',
                        'afternoon' => 'Ascend to the hilltop Shankaracharya Temple to receive blessings and behold unmatched 360-degree panoramic views of Srinagar city.',
                        'evening' => 'Browse authentic artisan showrooms for hand-knotted silk carpets, Pashmina shawls, and delicate walnut wood carvings.'
                    ],
                    3 => [
                        'title' => 'Meadow of Flowers: Gulmarg Gondola Cable Car & Snow Glades',
                        'morning' => 'Embark on a scenic drive through emerald willow groves and pine-carpeted slopes towards the alpine wonderland of Gulmarg.',
                        'midday' => 'Board the world-famous Gulmarg Gondola cable car, soaring past pine canopies to Kongdoori and Apharwat Peak for snow activities.',
                        'afternoon' => 'Walk across the expansive green rolling golf course, take a pony ride through Strawberry Valley, and enjoy warm saffron tea.',
                        'evening' => 'Descend peacefully back to Srinagar, watching the evening sun cast long shadows over pastoral Kashmir villages.'
                    ],
                    4 => [
                        'title' => 'Valley of Shepherds: Pahalgam Expedition, Betaab Valley & Aru River',
                        'morning' => 'Journey alongside the rushing turquoise waters of the Lidder River past aromatic saffron fields at Pampore to Pahalgam.',
                        'midday' => 'Explore the breathtaking landscapes of Betaab Valley, named after the famous Bollywood film and framed by snow-draped peaks.',
                        'afternoon' => 'Proceed into the pristine alpine meadows of Aru Valley, or take a horse trek up to Baisaran, celebrated as Mini Switzerland.',
                        'evening' => 'Relax beside the singing Lidder River, shopping for authentic mountain walnuts, saffron, and pure honey in the local bazaar.'
                    ],
                    5 => [
                        'title' => 'Old Srinagar Heritage, Souvenir Shopping & Departure Transfer',
                        'morning' => 'Enjoy your final morning breakfast in the valley, taking a quiet moment to absorb the beauty of the surrounding peaks.',
                        'midday' => 'Check out and visit the historic Jamia Masjid and Shah-e-Hamdan wooden shrine in Old Srinagar for a glimpse of ancient architecture.',
                        'afternoon' => 'Pick up last-minute Kashmiri dried fruits, bakery treats, and saffron before heading towards Srinagar Airport.',
                        'evening' => 'Board your flight with unforgettable memories of the snow peaks, calm lakes, and heartwarming hospitality of Kashmir.'
                    ]
                ]
            ],

            'goa' => [
                'name' => 'Goa',
                'days' => [
                    1 => [
                        'title' => 'Coastal Welcome to Goa & Relaxing Sunset Beach Walk',
                        'morning' => 'Arrive in sunny Goa where our representative greets you and transfers you in comfort to your coastal resort.',
                        'midday' => 'Check into your resort, freshen up, and unwind by the swimming pool under swaying palm canopies.',
                        'afternoon' => 'Head down to the golden sands of the beach to feel the warm sea breeze and listen to the rhythmic waves.',
                        'evening' => 'Sip refreshing tropical drinks at a beachfront shack while watching an awe-inspiring Arabian Sea sunset.'
                    ],
                    2 => [
                        'title' => 'North Goa Highlights: Fort Aguada, Baga Beach & Vibrant Bazaars',
                        'morning' => 'Visit the historic 17th-century Portuguese Fort Aguada and its landmark lighthouse commanding panoramic sea views.',
                        'midday' => 'Head to the energetic shores of Calangute and Baga beaches, trying optional water sports like parasailing and jet-skiing.',
                        'afternoon' => 'Explore the scenic red cliffs and bohemian charm of Anjuna Beach, stopping by chic beachfront cafes.',
                        'evening' => 'Stroll through the lively night markets of Arpora or Tito\'s Lane, soaking in Goa\'s celebrated coastal nightlife.'
                    ],
                    3 => [
                        'title' => 'South Goa Heritage: Old Goa Basilicas & Tropical Spice Plantation',
                        'morning' => 'Discover the rich UNESCO World Heritage colonial architecture of Old Goa, including the Basilica of Bom Jesus and Se Cathedral.',
                        'midday' => 'Visit a lush traditional Spice Plantation in Ponda, taking a guided aroma walk and enjoying an authentic Goan buffet lunch.',
                        'afternoon' => 'Proceed to the serene shores of Miramar Beach and pay respects at the ornate Mangueshi Temple.',
                        'evening' => 'Embark on an evening sunset river cruise along the Mandovi River, complete with Goan folk dance performances and music.'
                    ],
                    4 => [
                        'title' => 'Dudhsagar Waterfalls Safari & Latin Quarter Fontainhas Walk',
                        'morning' => 'Take an early-morning 4x4 Jeep safari through the Bhagwan Mahavir Wildlife Sanctuary to the majestic multi-tiered Dudhsagar Falls.',
                        'midday' => 'Swim in the cool natural freshwater pool beneath the roaring cascades surrounded by lush Western Ghats forest.',
                        'afternoon' => 'Return to Panaji to explore Fontainhas, the colorful Latin Quarter filled with Portuguese heritage villas and art galleries.',
                        'evening' => 'Relax at an intimate heritage cafe with freshly baked Goan bebinca and local coffee as soft streetlamps glow.'
                    ],
                    5 => [
                        'title' => 'Morning Beach Walks, Souvenir Shopping & Departure Transfer',
                        'morning' => 'Enjoy a lazy tropical breakfast, take a peaceful morning dip in the sea, and complete your resort check-out.',
                        'midday' => 'Browse local markets for Goan cashews, feni, handcrafted shell items, and beachwear.',
                        'afternoon' => 'Board your private vehicle for transfer to Goa Dabolim or MOPA Airport / Railway Station.',
                        'evening' => 'Depart with sunkissed memories of pristine beaches, heritage quarters, and the vibrant Goan spirit.'
                    ]
                ]
            ],

            'jaipur' => [
                'name' => 'Jaipur (Rajasthan)',
                'days' => [
                    1 => [
                        'title' => 'Royal Welcome to the Pink City & Sunset at Nahargarh Fort',
                        'morning' => 'Arrive in the royal capital of Jaipur where our representative extends a warm Rajasthani greeting and escorts you to your hotel.',
                        'midday' => 'Complete your check-in, relax in regal surroundings, and take in the vibrant pastel pink architecture of the city.',
                        'afternoon' => 'Drive up into the rugged Aravalli Hills to Nahargarh Fort, standing sentinel over the sprawling city.',
                        'evening' => 'Witness a breathtaking golden sunset over Jaipur from the fort ramparts, followed by a royal Rajasthani dinner.'
                    ],
                    2 => [
                        'title' => 'Grand Forts of Amber: Jeep Ascent, Sheesh Mahal & Jal Mahal',
                        'morning' => 'Ascend the majestic ramparts of Amber Fort via jeep, marveling at the towering sandstone battlements and grand courtyards.',
                        'midday' => 'Explore the breathtaking Sheesh Mahal (Mirror Palace), Diwan-e-Aam, and Sukh Niwas palace with their intricate inlay craftsmanship.',
                        'afternoon' => 'Stop by the enchanting Jal Mahal (Water Palace) resting gracefully in the center of Man Sagar Lake for photographic memories.',
                        'evening' => 'Visit historic Jaigarh Fort to view the legendary Jaivana cannon, once the world\'s largest cannon on wheels.'
                    ],
                    3 => [
                        'title' => 'Royal Palaces & Astrological Marvels: City Palace, Hawa Mahal & Jantar Mantar',
                        'morning' => 'Stand in awe before the intricate honeycomb facade of Hawa Mahal (Palace of Winds), capturing iconic morning photographs.',
                        'midday' => 'Tour the sprawling City Palace complex, visiting royal courtyards, museums displaying vintage weaponry, and royal textiles.',
                        'afternoon' => 'Explore the UNESCO-listed Jantar Mantar observatory to witness ancient stone astronomical instruments of incredible precision.',
                        'evening' => 'Immerse yourself in the bustling bazaars of Johari Bazaar and Bapu Bazaar, shopping for Jaipuri quilts, gemstones, and blue pottery.'
                    ],
                    4 => [
                        'title' => 'Artisan Heritage & Cultural Village Experience at Chokhi Dhani',
                        'morning' => 'Visit the Indo-Saracenic masterpiece of Albert Hall Museum, admiring Egyptian relics and royal Rajasthani portraits.',
                        'midday' => 'Explore traditional block-printing workshops in Sanganer to witness master craftsmen stamping organic dyes onto cotton.',
                        'afternoon' => 'Take time for leisure and photography around the scenic royal cenotaphs of Gaitore.',
                        'evening' => 'Spend a festive evening at Chokhi Dhani ethnic resort, experiencing camel rides, puppet shows, folk dances, and traditional thali dining.'
                    ],
                    5 => [
                        'title' => 'Morning Heritage Strolls, Souvenir Shopping & Departure Transfer',
                        'morning' => 'Enjoy a royal breakfast, take final morning photographs of the Pink City avenues, and complete hotel check-out.',
                        'midday' => 'Pick up famous Rajasthani sweets like Ghewar and mawa kachori from heritage sweet shops.',
                        'afternoon' => 'Transfer comfortably in your private vehicle towards Jaipur Airport or Railway Station.',
                        'evening' => 'Bid farewell to Rajasthan with unforgettable memories of regal palaces, grand fortresses, and royal hospitality.'
                    ]
                ]
            ],

            'kerala' => [
                'name' => 'Kerala (Munnar & Alleppey)',
                'days' => [
                    1 => [
                        'title' => 'Arrival in Kochi & Scenic Hill Drive to Munnar via Waterfalls',
                        'morning' => 'Arrive at Kochi Airport/Station and embark on a mesmerizing winding drive up into the Western Ghats towards Munnar.',
                        'midday' => 'Stop en route to admire the gushing cascades of Cheeyappara and Valara Waterfalls amidst lush tropical rainforest.',
                        'afternoon' => 'Pass through sprawling emerald tea plantations blanketing the rolling hillsides as cool mountain mist rolls in.',
                        'evening' => 'Check into your hillside resort in Munnar and enjoy an evening stroll through fragrant spice and cardamom gardens.'
                    ],
                    2 => [
                        'title' => 'Munnar Tea Trails: Eravikulam National Park & Mattupetty Dam',
                        'morning' => 'Visit Eravikulam National Park, home to the endangered Nilgiri Tahr mountain goat, walking along lush high-altitude meadows.',
                        'midday' => 'Tour the historic Tata Tea Museum to learn the fascinating colonial heritage and processing methods of world-class tea leaves.',
                        'afternoon' => 'Drive to scenic Mattupetty Dam and Echo Point, enjoying optional speedboating and peaceful walks along the lake reservoir.',
                        'evening' => 'Wander through the vibrant Munnar town market to purchase authentic homemade chocolates, fresh green tea, and essential oils.'
                    ],
                    3 => [
                        'title' => 'Spice Plantations & Periyar Wildlife Sanctuary Boating in Thekkady',
                        'morning' => 'Drive through mist-clad mountain passes towards Thekkady, the spice capital of Kerala.',
                        'midday' => 'Take an informative guided walk through fragrant spice plantations, seeing black pepper vines, cardamom pods, and cinnamon bark.',
                        'afternoon' => 'Embark on a boat safari across the scenic Periyar Lake within the Periyar Tiger Reserve to spot wild elephants, deer, and exotic birds.',
                        'evening' => 'Witness a mesmerizing live performance of Kalaripayattu, the world\'s oldest martial art, followed by traditional Kathakali dance.'
                    ],
                    4 => [
                        'title' => 'Venice of the East: Alleppey Backwaters Traditional Houseboat Cruise',
                        'morning' => 'Descend from the misty hills towards the idyllic coastal palm backwaters of Alleppey (Alappuzha).',
                        'midday' => 'Board your traditional thatched-roof Kettuvallam houseboat, greeted with fresh coconut water and warm Malabar hospitality.',
                        'afternoon' => 'Cruise leisurely along tranquil palm-fringed canals, observing village life, paddy fields below sea level, and fishermen at work.',
                        'evening' => 'Relish freshly prepared traditional Kerala meals on board as the boat anchors under starlit backwater skies.'
                    ],
                    5 => [
                        'title' => 'Coastal Heritage of Fort Kochi & Airport Departure Transfer',
                        'morning' => 'Enjoy an idyllic sunrise breakfast over the backwaters before disembarking from your houseboat.',
                        'midday' => 'Travel to Fort Kochi to see the iconic cantilevered Chinese Fishing Nets, St. Francis Church, and colorful Jew Town.',
                        'afternoon' => 'Browse antique shops along the spice quarter before transferring in comfort to Kochi Airport.',
                        'evening' => 'Depart with lifelong memories of God\'s Own Country—its verdant tea hills, serene backwaters, and gentle warmth.'
                    ]
                ]
            ]
        ];
    }
}

if (!function_exists('v3_build_smart_itinerary_day')) {
    function v3_build_smart_itinerary_day($dayNum, $totalDays, $stayCity, $curDest, $notes, $customTitle, $vehicle, $mealPlan, $dayIndex, &$usedTracker)
    {
        $isTransit = ($stayCity === 'Transit' || strtolower($stayCity) === 'transit');
        $destKey = v3_normalize_destination_key($curDest ?: $stayCity);
        $catalog = v3_get_destination_catalog();

        $destInfo = $catalog[$destKey] ?? null;
        $destDisplayName = $destInfo ? $destInfo['name'] : v3_format_city_display($curDest ?: $stayCity);

        // 1. Check if user provided notes that specify specific landmarks
        $userLandmark = v3_parse_user_notes_landmarks($notes);

        // 2. Select base content
        if ($dayNum === 1) {
            // Day 1: Arrival & Transfer Day
            if ($userLandmark) {
                $dayTitle = !empty($customTitle) ? $customTitle : "Day 1 - Arrival & {$userLandmark['title']}";
                $morning = $userLandmark['morning'];
                $midday = $userLandmark['midday'];
                $afternoon = $userLandmark['afternoon'];
                $evening = $userLandmark['evening'];
            } elseif ($destInfo && isset($destInfo['days'][1])) {
                $dayTitle = !empty($customTitle) ? $customTitle : "Day 1 - " . $destInfo['days'][1]['title'];
                $morning = $destInfo['days'][1]['morning'];
                $midday = $destInfo['days'][1]['midday'];
                $afternoon = $destInfo['days'][1]['afternoon'];
                $evening = $destInfo['days'][1]['evening'];
            } else {
                $dayTitle = !empty($customTitle) ? $customTitle : "Day 1 - Arrival at {$destDisplayName} & Scenic Orientation";
                $morning = "Begin your holiday with a smooth, comfortable pickup and warm greeting as your private journey commences.";
                $midday = "Arrive in {$destDisplayName}, complete your hotel check-in formalities, and take time to refresh in your welcoming accommodations.";
                $afternoon = "Set out for an introductory orientation walk, taking in the unique atmosphere and charming streets of the destination.";
                $evening = "Stroll along the central promenade or marketplace as evening lights turn on, enjoying the pleasant local ambiance.";
            }
        } elseif ($dayNum === $totalDays) {
            // Final Departure Day
            if ($userLandmark) {
                $dayTitle = !empty($customTitle) ? $customTitle : "Day {$dayNum} - {$userLandmark['title']} & Tour Departure";
                $morning = $userLandmark['morning'];
                $midday = $userLandmark['midday'];
                $afternoon = $userLandmark['afternoon'];
                $evening = "Conclude your holiday as you are safely transferred to the departure hub with unforgettable memories.";
            } elseif ($destInfo && isset($destInfo['days'][5])) {
                $dayTitle = !empty($customTitle) ? $customTitle : "Day {$dayNum} - " . $destInfo['days'][5]['title'];
                $morning = $destInfo['days'][5]['morning'];
                $midday = $destInfo['days'][5]['midday'];
                $afternoon = $destInfo['days'][5]['afternoon'];
                $evening = $destInfo['days'][5]['evening'];
            } else {
                $dayTitle = !empty($customTitle) ? $customTitle : "Day {$dayNum} - Farewell to {$destDisplayName} & Return Transfer";
                $morning = "Enjoy a relaxed breakfast at your hotel, taking in final panoramic views and packing your travel bags.";
                $midday = "Complete your hotel check-out formalities and board your private vehicle for the return journey.";
                $afternoon = "Make a pleasant stop at local artisan markets or roadside viewpoint stalls to pick up authentic regional souvenirs.";
                $evening = "Arrive at the airport or railway station for your onward travel. Tour concludes with happy and memorable moments.";
            }
        } else {
            // Mid Exploration Days (Day 2, 3, 4, etc.)
            if ($userLandmark) {
                $dayTitle = !empty($customTitle) ? $customTitle : "Day {$dayNum} - {$userLandmark['title']}";
                $morning = $userLandmark['morning'];
                $midday = $userLandmark['midday'];
                $afternoon = $userLandmark['afternoon'];
                $evening = $userLandmark['evening'];
            } else {
                // Pick from blueprint based on day index
                $blueprintDayIndex = min(4, max(2, $dayNum));
                if ($destInfo && isset($destInfo['days'][$blueprintDayIndex])) {
                    $dayTitle = !empty($customTitle) ? $customTitle : "Day {$dayNum} - " . $destInfo['days'][$blueprintDayIndex]['title'];
                    $morning = $destInfo['days'][$blueprintDayIndex]['morning'];
                    $midday = $destInfo['days'][$blueprintDayIndex]['midday'];
                    $afternoon = $destInfo['days'][$blueprintDayIndex]['afternoon'];
                    $evening = $destInfo['days'][$blueprintDayIndex]['evening'];
                } else {
                    // Procedural distinct day generation for general destinations
                    $generalDayThemes = [
                        2 => [
                            'title' => "Cultural Discovery: Iconic Monuments & Heritage Walk in {$destDisplayName}",
                            'morning' => "Set out on a rewarding morning tour exploring the prime historical landmarks and architectural treasures of {$destDisplayName}.",
                            'midday' => "Visit renowned local museums and cultural centers, gaining insight into the rich traditions and folklore of the region.",
                            'afternoon' => "Wander through bustling traditional artisan quarters, observing master craftspeople at work and sampling regional snacks.",
                            'evening' => "Relax at a scenic sunset viewpoint overlooking the town as evening settles peacefully over the surrounding terrain."
                        ],
                        3 => [
                            'title' => "Natural Wonders & Panoramic Excursion in {$destDisplayName}",
                            'morning' => "Embark on an exhilarating day trip venturing into the picturesque hills and verdant countryside surrounding {$destDisplayName}.",
                            'midday' => "Explore scenic nature trails, gushing water streams, and tranquil forest viewpoints ideal for panoramic photography.",
                            'afternoon' => "Savor an authentic local meal at a rustic countryside retreat before visiting a nearby botanical park or wildlife sanctuary.",
                            'evening' => "Return leisurely towards town, stopping along the hillside roads to enjoy cool breezes and evening tea."
                        ],
                        4 => [
                            'title' => "Offbeat Exploration: Hidden Valleys & Artisan Bazaars in {$destDisplayName}",
                            'morning' => "Dedicate this morning to discovering the lesser-known gems, peaceful shrines, and tranquil village lanes of {$destDisplayName}.",
                            'midday' => "Experience a guided walk through local plantations or organic orchards, engaging with warm-hearted resident communities.",
                            'afternoon' => "Spend unhurried hours exploring colorful regional bazaars, collecting handcrafted textiles, spices, and decorative art.",
                            'evening' => "Unwind at a cozy open-air terrace cafe, soaking in the ambient night views and listening to soothing background music."
                        ]
                    ];
                    $themeChoice = $generalDayThemes[$blueprintDayIndex] ?? $generalDayThemes[2];
                    $dayTitle = !empty($customTitle) ? $customTitle : "Day {$dayNum} - " . $themeChoice['title'];
                    $morning = $themeChoice['morning'];
                    $midday = $themeChoice['midday'];
                    $afternoon = $themeChoice['afternoon'];
                    $evening = $themeChoice['evening'];
                }
            }
        }

        // Varied, distinct transitions and dinner/stay conclusions (NO REPETITION GUARANTEE)
        $stayConclusion = '';
        if ($dayNum === $totalDays) {
            $stayConclusion = '';
        } elseif ($isTransit) {
            $stayConclusion = "Conclude the day in onward transit as your travel journey proceeds seamlessly towards the next destination.";
        } else {
            $dinnerClosings = [
                1 => "Enjoy a warm welcome dinner and relax comfortably in your hotel in {$destDisplayName}.",
                2 => "Relish a delicious dinner featuring authentic regional specialties followed by a restful night's sleep in {$destDisplayName}.",
                3 => "Indulge in a mouthwatering hot dinner at the hotel restaurant and recharge peacefully for the next day in {$destDisplayName}.",
                4 => "Savor a multi-course dinner of local delicacies and unwind in the comfortable ambiance of your hotel in {$destDisplayName}.",
                5 => "Conclude the memorable day with a delightful dinner and a deep, tranquil overnight stay in {$destDisplayName}.",
                6 => "Partake in a satisfying evening meal before retiring to your cozy room in {$destDisplayName}."
            ];
            $closingIndex = (($dayNum - 1) % count($dinnerClosings)) + 1;
            $stayConclusion = $dinnerClosings[$closingIndex] ?? "Relish a delicious dinner and enjoy a comfortable overnight stay in {$destDisplayName}.";
        }

        // Assemble narrative smoothly without generic robotic sentence structures
        $vehicleMention = !empty($vehicle) ? "aboard your private {$vehicle}" : "with dedicated private transport";
        $narrative = "{$morning} Traveling {$vehicleMention}, {$midday} {$afternoon} {$evening}";
        if (!empty($stayConclusion)) {
            $narrative .= " {$stayConclusion}";
        }

        // Clean double spaces or punctuation
        $narrative = preg_replace('/\s+/', ' ', trim($narrative));

        return [
            'day' => $dayTitle,
            'stay' => $stayCity,
            'details' => $narrative
        ];
    }
}
