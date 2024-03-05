<?php

require_once 'header.php';
?>
<div id="searchByMobile">
    <div class="formGroup">
        <input type="number" name="mobile" id="mobile" placeholder="Mobile *" pattern="[0-9]*" inputmode="numeric"
            autofocus required>
        <label class="error" id="mobileNumberError">Please enter valid mobile no.</label>
    </div>
    <div class="formGroup btnBox">
        <label></label>
        <button id="searchUser" class="submitBtn">Continue</button>
    </div>
</div>
<div id="profileBox">
    <div class="formGroup">
        <input type="text" name="name" id="name" placeholder="Full name *" required>
        <label class="error" id="nameError">Please enter a valid name.</label>
    </div>
    <div class="formGroup">
        <input type="number" id="resMobile" placeholder="Mobile *" pattern="[0-9]*" inputmode="numeric" readonly>
        <label class="error" id="mobileNumberError">Please enter valid mobile no.</label>
    </div>
    <div class="formGroup">
        <input type="email" name="email" id="email" placeholder="Email *" required>
        <label class="error" id="emailError">Please enter a valid email address.</label>
    </div>
    <div class="formGroup">
        <input type="number" name="amount" id="amount" placeholder="Amount *" value="<?php echo $_GET['amount'] ?>"
            pattern="[0-9]*" inputmode="numeric" required>
        <label class="error" id="amountError">Please enter a valid amount only in number.</label>
    </div>
    <div class="formGroup">
        <textarea name="message" id="message" placeholder="Any message"></textarea>
    </div>
    <div class="formGroup btnBox">
        <input type="hidden" name="userToken" id="userToken">
        <button id="generatePayment" class="submitBtn">Pay Now</button>
    </div>
</div>
<?php

require_once 'footer.php';

?>