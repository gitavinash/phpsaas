<?php

require_once 'header.php';

?>

<!-- Hero Section -->
<section class="hero"
    style="background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('<?php echo USER_IMAGES ?>color-clinic-hero.jpeg') center/cover no-repeat; padding-top: 200px; padding-bottom: 150px; align-items: center; min-height: 70vh; display: flex; justify-content: center; text-align: center;">
    <div class="container" style="max-width: 900px; margin: 0 auto; padding: 0 20px;">
        <div class="hero-content reveal-on-load">
            <h1 class="hero-premium-headline"
                style="margin-bottom: 20px; font-size: 4.5rem; color: var(--white); text-shadow: 0 4px 20px rgba(0,0,0,0.3);">
                Color Clinic
            </h1>
            <p
                style="font-size: 1.35rem; color: #fff; line-height: 1.6; text-shadow: 0 2px 10px rgba(0,0,0,0.5); margin: 0 auto; max-width: 700px;">
                Our expert team employs scientific color analysis and refined intuition to transform personal styling,
                interior environments, and energetic alignments. We empower you to see and utilize color the way the
                experts do.
            </p>
        </div>
    </div>
</section>



<!-- <section class="spatial-hues-section soft-green-bg" style="padding: 100px 8%;">
    <div class="container" style="display: flex; flex-wrap: wrap; align-items: center; gap: 60px; margin: 0 auto; max-width: 1400px; width: 100%;">
        
        <div class="image-content reveal" style="flex: 1; min-width: 320px;">
             <div class="icon-box-wrapper" style="border: 1px solid rgba(0,0,0,0.05); border-radius: 24px; padding: 60px 40px; text-align: center; background: #fafafa; box-shadow: 0 10px 30px rgba(0,0,0,0.03); transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-10px)'" onmouseout="this.style.transform='translateY(0)'">
                 <div style="width: 80px; height: 80px; background: var(--hue-pink); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px;">
                     <i class="fa-solid fa-house" style="font-size: 2.5rem; color: var(--text-main);"></i>
                 </div>
                 <h2 class="serif-normal" style="font-size: 2.5rem; margin-bottom: 15px; color: var(--text-main);">Spatial Hues</h2>
                 <p style="color: #777; font-size: 1.1rem;">Transform your environments.</p>
             </div>
        </div>

        <div class="text-content reveal" style="flex: 1; min-width: 320px;">
            <p style="font-size: 1.2rem; color: #555; line-height: 1.8; margin-bottom: 30px;">
                Understanding undertones is the foundational secret to creating spaces that feel inherently right. Without this knowledge, selecting paint, furnishings, and finishes becomes a guessing game. Hue Culture provides a transparent, teachable system that takes the mystery out of color selection for spaces.
            </p>
            <a href="spatial-hues.php" class="btn-primary" style="display: inline-block;">Know More</a>
        </div>
    </div>
</section> -->
<section style="background-color: var(--white); padding: 100px 8%;">
    <div class="container"
        style="display: flex; flex-wrap: wrap; align-items: center; gap: 60px; margin: 0 auto; max-width: 1400px; width: 100%;">

        <div class="image-content reveal" style="flex: 1; min-width: 320px;">
            <div class="icon-box-wrapper"
                style="border: 1px solid rgba(0,0,0,0.05); border-radius: 24px; padding: 60px 40px; text-align: center; background: #fafafa; box-shadow: 0 10px 30px rgba(0,0,0,0.03); transition: transform 0.3s; display: flex; justify-content: center; align-items: center;"
                onmouseover="this.style.transform='translateY(-10px)'"
                onmouseout="this.style.transform='translateY(0)'">
                <img src="<?php echo USER_IMAGES ?>color-clinic/spaces.png" style="max-height: 250px; width: auto; object-fit: contain;" alt="Spaces">
            </div>
        </div>

        <div class="text-content reveal" style="flex: 1; min-width: 320px;">
            <h2 class="serif-normal" style="font-size: 2.8rem; margin-bottom: 20px; color: var(--charcoal);">Spaces</h2>
            <p style="font-size: 1.2rem; color: #555; line-height: 1.8; margin-bottom: 30px;">
                Understanding undertones is the foundational secret to creating spaces that feel inherently right.
                Without this knowledge, selecting paint, furnishings, and finishes becomes a guessing game. Hue Culture
                provides a transparent, teachable system that takes the mystery out of color selection for spaces.
            </p>
            <a href="spatial-hues.php" class="btn-primary light-pink-bg" style="display: inline-block;">Know More</a>
        </div>
    </div>
</section>

<section class="soft-green-bg" style="padding: 100px 8%;">
    <div class="container"
        style="display: flex; flex-wrap: wrap-reverse; align-items: center; gap: 60px; margin: 0 auto; max-width: 1400px; width: 100%;">

        <div class="text-content reveal" style="flex: 1; min-width: 320px;">
            <h2 class="serif-normal" style="font-size: 2.8rem; margin-bottom: 20px; color: var(--charcoal);">Color Analysis</h2>
            <p style="font-size: 1.2rem; color: #555; line-height: 1.8; margin-bottom: 30px;">
                Our color analysis system goes beyond typical palettes. We find the specific shades that align perfectly
                with your natural coloring, elevating your personal style and boosting confidence in every outfit you
                choose. Connect with your true aesthetic.
            </p>
            <a href="services/" class="btn-primary light-pink-bg" style="display: inline-block;">Know More</a>
        </div>

        <div class="image-content reveal" style="flex: 1; min-width: 320px;">
            <div class="icon-box-wrapper"
                style="border: 1px solid rgba(0,0,0,0.05); border-radius: 24px; padding: 60px 40px; text-align: center; background: var(--white); box-shadow: 0 10px 30px rgba(0,0,0,0.03); transition: transform 0.3s; display: flex; justify-content: center; align-items: center;"
                onmouseover="this.style.transform='translateY(-10px)'"
                onmouseout="this.style.transform='translateY(0)'">
                <img src="<?php echo USER_IMAGES ?>color-clinic/personal-colour-analysis.png" style="max-height: 250px; width: auto; object-fit: contain;"
                    alt="Personal Color Analysis">
            </div>
        </div>

    </div>
</section>

<section style="background-color: var(--white); padding: 100px 8%;">
    <div class="container"
        style="display: flex; flex-wrap: wrap; align-items: center; gap: 60px; margin: 0 auto; max-width: 1400px; width: 100%;">

        <div class="image-content reveal" style="flex: 1; min-width: 320px;">
            <div class="icon-box-wrapper"
                style="border: 1px solid rgba(0,0,0,0.05); border-radius: 24px; padding: 60px 40px; text-align: center; background: #fafafa; box-shadow: 0 10px 30px rgba(0,0,0,0.03); transition: transform 0.3s; display: flex; justify-content: center; align-items: center;"
                onmouseover="this.style.transform='translateY(-10px)'"
                onmouseout="this.style.transform='translateY(0)'">
                <img src="<?php echo USER_IMAGES ?>color-clinic/styling.png" style="max-height: 250px; width: auto; object-fit: contain;" alt="Styling">
            </div>
        </div>

        <div class="text-content reveal" style="flex: 1; min-width: 320px;">
            <h2 class="serif-normal" style="font-size: 2.8rem; margin-bottom: 20px; color: var(--charcoal);">Styling</h2>
            <p style="font-size: 1.2rem; color: #555; line-height: 1.8; margin-bottom: 30px;">
                Discover how the right colors can completely elevate your wardrobe. We apply our scientific color theory to personal styling, ensuring that every garment you choose harmonizes with your natural contrast and undertone. Dress with intention and confidence.
            </p>
            <a href="spatial-hues.php" class="btn-primary light-pink-bg" style="display: inline-block;">Know More</a>
        </div>
    </div>
</section>

<section class="soft-green-bg" style="padding: 100px 8%;">
    <div class="container"
        style="display: flex; flex-wrap: wrap-reverse; align-items: center; gap: 60px; margin: 0 auto; max-width: 1400px; width: 100%;">

        <div class="text-content reveal" style="flex: 1; min-width: 320px;">
            <h2 class="serif-normal" style="font-size: 2.8rem; margin-bottom: 20px; color: var(--charcoal);">Energy Palettes</h2>
            <p style="font-size: 1.2rem; color: #555; line-height: 1.8; margin-bottom: 30px;">
                Color is energy. We explore how different hues impact your mood, focus, and overall well-being. By curating your energetic palette, we help you align your surroundings with your personal goals, bringing balance and vitality into your daily life.
            </p>
            <a href="services/" class="btn-primary light-pink-bg" style="display: inline-block;">Know More</a>
        </div>

        <div class="image-content reveal" style="flex: 1; min-width: 320px;">
            <div class="icon-box-wrapper"
                style="border: 1px solid rgba(0,0,0,0.05); border-radius: 24px; padding: 60px 40px; text-align: center; background: var(--white); box-shadow: 0 10px 30px rgba(0,0,0,0.03); transition: transform 0.3s; display: flex; justify-content: center; align-items: center;"
                onmouseover="this.style.transform='translateY(-10px)'"
                onmouseout="this.style.transform='translateY(0)'">
                <img src="<?php echo USER_IMAGES ?>color-clinic/energy.png" style="max-height: 250px; width: auto; object-fit: contain;" alt="Energy Palettes">
            </div>
        </div>

    </div>
</section>

<section style="background-color: var(--white); padding: 100px 8%;">
    <div class="container"
        style="display: flex; flex-wrap: wrap; align-items: center; gap: 60px; margin: 0 auto; max-width: 1400px; width: 100%;">

        <div class="image-content reveal" style="flex: 1; min-width: 320px;">
            <div class="icon-box-wrapper"
                style="border: 1px solid rgba(0,0,0,0.05); border-radius: 24px; padding: 60px 40px; text-align: center; background: #fafafa; box-shadow: 0 10px 30px rgba(0,0,0,0.03); transition: transform 0.3s; display: flex; justify-content: center; align-items: center;"
                onmouseover="this.style.transform='translateY(-10px)'"
                onmouseout="this.style.transform='translateY(0)'">
                <img src="<?php echo USER_IMAGES ?>color-clinic/collaborations.png" style="max-height: 250px; width: auto; object-fit: contain;"
                    alt="Collaborations">
            </div>
        </div>

        <div class="text-content reveal" style="flex: 1; min-width: 320px;">
            <h2 class="serif-normal" style="font-size: 2.8rem; margin-bottom: 20px; color: var(--charcoal);">Collaborations</h2>
            <p style="font-size: 1.2rem; color: #555; line-height: 1.8; margin-bottom: 30px;">
                We partner with designers, brands, and creatives to bring the Hue Color System to life across various mediums. Our expertise in color psychology and aesthetics ensures that every collaborative project resonates powerfully with its intended audience.
            </p>
            <a href="spatial-hues.php" class="btn-primary light-pink-bg" style="display: inline-block;">Know More</a>
        </div>
    </div>
</section>

<section class="soft-green-bg" style="padding: 100px 8%;">
    <div class="container"
        style="display: flex; flex-wrap: wrap-reverse; align-items: center; gap: 60px; margin: 0 auto; max-width: 1400px; width: 100%;">

        <div class="text-content reveal" style="flex: 1; min-width: 320px;">
            <h2 class="serif-normal" style="font-size: 2.8rem; margin-bottom: 20px; color: var(--charcoal);">Colorana</h2>
            <p style="font-size: 1.2rem; color: #555; line-height: 1.8; margin-bottom: 30px;">
                Uncover your unique Color DNA. This deeply personalized service maps out the exact spectrum of colors that belong to you. It's a transformative experience that provides lifelong guidance for everything from interior design choices to personal branding.
            </p>
            <a href="services/" class="btn-primary light-pink-bg" style="display: inline-block;">Know More</a>
        </div>

        <div class="image-content reveal" style="flex: 1; min-width: 320px;">
            <div class="icon-box-wrapper"
                style="border: 1px solid rgba(0,0,0,0.05); border-radius: 24px; padding: 60px 40px; text-align: center; background: var(--white); box-shadow: 0 10px 30px rgba(0,0,0,0.03); transition: transform 0.3s; display: flex; justify-content: center; align-items: center;"
                onmouseover="this.style.transform='translateY(-10px)'"
                onmouseout="this.style.transform='translateY(0)'">
                <img src="<?php echo USER_IMAGES ?>color-clinic/color-dna.png" style="max-height: 250px; width: auto; object-fit: contain;"
                    alt="Color DNA">
            </div>
        </div>

    </div>
</section>


<?php

require_once 'footer.php';

?>