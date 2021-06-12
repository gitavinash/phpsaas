<h2 class="heading"><?php echo 'Add New '.ucwords($urlStructure[1]) ?><span>Create new subscriptions plans for new
        members.</span>
</h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>About Subscription</li>
            <li class="step-box" id="step_2"><span>2</span>Subscription Validity</li>
        </ul>
        <div class="steps-box">
            <form class="main-form" action="#" id="addNewSubscriptionForm">
                <div class="step-open-box step_1_box">
                    <div class="form-group">
                        <input class="form-control" type="text" id="name" />
                        <label for="name">Name</label>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" name="" id="text"></textarea>
                        <label for="text">Name</label>
                    </div>
                </div>
                <div class="step-open-box step_2_box" style="display:none">
                    Hello i'm step 2
                </div>
            </form>
        </div>
    </section>
</div>