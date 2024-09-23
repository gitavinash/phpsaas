<h2 class="heading"><?php echo 'Add New ' . ucwords($urlStructure[1]) ?><span>Add new domain tld for domain
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
                            <h1>Domain TLD's details</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/domain-tlds.png" alt="Add new domain tld">
                        </div>
                        <div class="form-group">
                            <input class="form-control" id="name" type="text" />
                            <label class="form-lable" id="nameLabel">
                                Domain TLD's ( Domain extension: Like: .com, .net, .in etc ). <sup>*</sup>
                            </label>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" id="textarea"></textarea>
                            <label class="form-lable" id="textareaLabel">Domain details</label>
                        </div>
                        <div class="form-group">
                            <input class="form-control" id="mrpPrice" type="number" onwheel="this.blur()" />
                            <label class="form-lable" id="mrpPriceLabel">MRP price. ( in ₹ ) <sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input class="form-control" id="sellingPrice" type="number" onwheel="this.blur()" />
                            <label class="form-lable" id="sellingPriceLabel">Selling price. ( in ₹ )
                                <sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input class="form-control" id="costPrice" type="number" onwheel="this.blur()" />
                            <label class="form-lable" id="costPriceLabel">Cost on domain. ( in ₹ )</label>
                        </div>
                        <div class="form-group-box">
                            <p>Mark domain as featured</p>
                            <div class="box-2 left p-right-10">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input type="radio" id="radio1" name="demo02" value="yes" />
                                        <label class="radio-button" for="radio1Lable">Yes</label>
                                    </div>
                                </div>
                            </div>
                            <div class="box-2 left p-left-10">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input type="radio" id="radio2" name="demo02" value="no" checked />
                                        <label class="radio-button" for="radio2Lable">No</label>
                                    </div>
                                </div>
                            </div>
                            <!-- </div>
                        <div class="form-group-box">
                            <p>Select Multiple button ( any one )</p>
                            <div class="box-2 left p-right-10">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input type="checkbox" id="check1" name="demo02" value="yes" />
                                        <label class="radio-button" for="check1Lable">Hello World 11</label>
                                    </div>
                                </div>
                            </div>
                            <div class="box-2 left p-left-10">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input type="checkbox" id="check2" name="demo02" value="yes" />
                                        <label class="radio-button" for="check2Lable">Hello World 12</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <p>Select Multiple button ( any one )</p>
                            <select class="selectJS form-control" name="select" id="select">
                                <option value="">Select drop down</option>
                                <option value="2">Hello 21</option>
                                <option value="2">Hello 22</option>
                                <option value="2">Hello 23</option>
                                <option value="2">Hello 24</option>
                                <option value="2">Hello 25</option>
                                <option value="2">Hello 26</option>
                                <option value="2">Hello 27</option>
                                <option value="2">Hello 28</option>
                            </select>
                        </div> -->
                            <div class="box-2">
                                <div class="form-group">
                                    <button id="publish">Add new Domain TLD's</button>
                                </div>
                            </div>
                        </div>
                </section>
            </form>
        </div>
    </section>
</div>