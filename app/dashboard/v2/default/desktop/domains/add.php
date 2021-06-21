<h2 class="heading"><?php echo 'Add New '.ucwords($urlStructure[1]) ?><span>Add new domain tld for domain
        registration.</span>
</h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Domain details</li>
            <!-- <li class="step-box" id="step_2"><span>2</span>Domain validity</li> -->
        </ul>
        <div class="steps-box">
            <form action="#" id="addNewForm">
                <section class="form">
                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1>Domain details</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/domain-tlds.png" alt="Add new domain tld">
                        </div>
                        <div class="form-group">
                            <input class="form-control" id="name" type="text" />
                            <label id="nameLabel">Domain package name.</label>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" id="textarea"></textarea>
                            <label id="textareaLabel">Domain details</label>
                        </div>
                        <div class="form-group">
                            <select class="form-control" id="select">
                                <option value="">Hello</option>
                                <option value="2">Hello 2</option>
                            </select>
                            <label class="heading" id="selectLabel">Select drop down</label>
                        </div>
                        <!-- <div class="form-group">
                            <p><strong>Select radio button ( any one )</strong></p>

                            <div class="radio-group">
                                <input type="radio" class="hide" id="option1">
                                <label for="option1" class="radio-lable">
                                    <div>Option 1</div>
                                </label>
                            </div>
                            <div class="radio-group">
                                <input type="radio" class="hide" id="option2">
                                <label for="option2" class="radio-lable">
                                    <div>Option 2</div>
                                </label>
                            </div>
                        </div> -->
                    </div>
                </section>
            </form>
        </div>
    </section>
</div>