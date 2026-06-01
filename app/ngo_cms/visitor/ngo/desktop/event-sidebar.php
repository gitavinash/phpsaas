<aside class="sidebar">
    <?php

    if ($event_map != '') { ?>

        <div class="sidebar-widget">
            <h3>Event Location</h3>
            <?php echo $event_map; ?>
        </div>

    <?php
    }

    ?>

    <div class="sidebar-widget">
        <h3>Registration</h3>
        <p style="font-size: 0.9rem; margin-bottom: 1rem">
            Pre-registration helps us manage resources better. It takes only 2
            minutes.
        </p>
        <form>
            <input
                type="text"
                placeholder="Full Name"
                style="
                width: 100%;
                padding: 0.8rem;
                margin-bottom: 0.8rem;
                border: 1px solid #ddd;
                border-radius: 5px;
              " />
            <input
                type="tel"
                placeholder="Phone Number"
                style="
                width: 100%;
                padding: 0.8rem;
                margin-bottom: 0.8rem;
                border: 1px solid #ddd;
                border-radius: 5px;
              " />
            <button class="btn btn-primary" style="width: 100%">
                Register Now
            </button>
        </form>
    </div>
</aside>