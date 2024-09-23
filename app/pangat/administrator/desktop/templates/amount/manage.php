<?php

if (!defined('BREAKFAST_SLOT_PRICE')) {

    define('BREAKFAST_SLOT_PRICE', 1500);
}

if (!defined('LUNCH_SLOT_PRICE')) {

    define('LUNCH_SLOT_PRICE', 3100);
}

if (!defined('DINNER_SLOT_PRICE')) {

    define('DINNER_SLOT_PRICE', 3100);
}

?>

<h2>Manage pangat booking amount </h2>
<div class="options-bar">
    <div class="left-box">
        <table>
            <thead>
                <tr>
                    <th>S No.</th>
                    <th>Slots</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        1.
                    </td>
                    <td>
                        Breakfast
                    </td>
                    <td>
                        <input type="number" onwheel="this.blur()" id="breakfastAmount" placeholder="Breakfast amount" class="form-control"
                            value="<?php echo BREAKFAST_SLOT_PRICE ?>"
                            style=" border: 1px solid #333; border-radius: 4px;">
                    </td>
                    <td>
                        <input type="submit" id="breakfast" class="btn updateSlotAmount"
                            value="Update Breakfast Amount">
                    </td>
                </tr>
                <tr>
                    <td>
                        2.
                    </td>
                    <td>
                        Lunch
                    </td>
                    <td>
                        <input type="number" onwheel="this.blur()" id="lunchAmount" placeholder="Lunch amount" class="form-control"
                            value="<?php echo LUNCH_SLOT_PRICE ?>" style=" border: 1px solid #333; border-radius: 4px;">
                    </td>
                    <td>
                        <input type="submit" id="lunch" class="btn updateSlotAmount" value="Update Lunch Amount">
                    </td>
                </tr>
                <tr>
                    <td>
                        3.
                    </td>
                    <td>
                        Dinner
                    </td>
                    <td>
                        <input type="number" onwheel="this.blur()" id="dinnerAmount" placeholder="Dinner amount" class="form-control"
                            value="<?php echo DINNER_SLOT_PRICE ?>"
                            style=" border: 1px solid #333; border-radius: 4px;">
                    </td>
                    <td>
                        <input type="submit" id="dinner" class="btn updateSlotAmount" value="Update Dinner Amount">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>