<?php

    $url="/dashboard/leads/list/?leadStatus=5&daterange={$startDate} - {$endDate}";

?>
<a href="<?php echo $url ?>">
    <div class="widget-info" style="background: #E6E6FA" id="totalDuplicateLeads">
        <div class="info-box">
            <p>Duplicate enquiries
                <span id="totalDuplicateLeadsValue"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif"
                        alt=""></span>
                </span>
            </p>
        </div>
    </div>
</a>