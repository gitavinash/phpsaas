<h2 class="heading"><?php echo 'Add New country' ?><span>Country that we are offering VISA services</span></h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Visa package details</li>
        </ul>
        <div class="steps-box">
            <form action="#" method="POST" id="addNewForm">
                <section class="form">
                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1>Enter package details</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/hosting.png" alt="Add new Hosting tld">
                        </div>
                        <div class="form-group">
                            <p><strong>Country feature image<sup>*</sup></strong></p>
                            <div id="dropZone">
                                <span class="defaultText">Drag & Drop files here or click to upload</span>
                            </div>
                            <input type="file" id="fileInput" name="featureImage[]" data-limit="1"
                                accept=".jpg, .jpeg, .png" style="display: none;" />
                        </div>
                        <div class="form-group">
                            <input id="name" name="name" type="text" required />
                            <label class="form-lable">Name<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="code" name="code" type="text" />
                            <label class="form-lable">Country code<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <label class="form-lable">Documents Required</label>
                            <div class="editableBox" id="requirements" contenteditable="true"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-lable">Important Information</label>
                            <div class="editableBox" id="information" contenteditable="true"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-lable">FAQ's</label>
                            <div class="editableBox" id="faq" contenteditable="true"></div>
                        </div>
                        <div class="form-group-box">
                            <p>VISA service options<sup>*</sup></p>
                            <div class="box-2 left p-right-10">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input type="checkbox" id="visasType1" name="visaTypes" value="tourist" />
                                        <label class="radio-button" for="visasType1Label">Tourist</label>
                                    </div>
                                </div>
                            </div>
                            <div class="box-2 left p-right-10">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input type="checkbox" id="visasType2" name="visaTypes" value="business" />
                                        <label class="radio-button" for="visasType2Label">Business</label>
                                    </div>
                                </div>
                            </div>
                            <div class="box-2 left p-right-10">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input type="checkbox" id="visasType3" name="visaTypes" value="transit" />
                                        <label class="radio-button" for="visasType3Label">Transit</label>
                                    </div>
                                </div>
                            </div>
                            <div class="box-2 left p-right-10">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input type="checkbox" id="visasType4" name="visaTypes" value="student" />
                                        <label class="radio-button" for="visasType4Label">Student</label>
                                    </div>
                                </div>
                            </div>
                            <div class="box-2 left p-right-10">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input type="checkbox" id="visasType5" name="visaTypes" value="renewal" />
                                        <label class="radio-button" for="visasType5Label">Renewal</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-box">
                            <p>Mark featured<sup>*</sup></p>
                            <div class="box-5 left p-right-10">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input type="radio" id="isFeatured1" name="isFeatured" value="1" />
                                        <label class="radio-button" for="visasType1Label">Yes</label>
                                    </div>
                                </div>
                            </div>
                            <div class="box-5 left p-right-10">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input type="radio" id="isFeatured2" name="isFeatured" value="0" checked />
                                        <label class="radio-button" for="visasType2Label">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-1">
                            <div class="form-group">
                                <a href="#" class="btn popUpBtn" id="addNewCountry"><i class="fi-rr-check"></i> <span
                                        id="btnText">Add country</span>
                                    <span id="loadingSpinner" style="display:none;"> <i
                                            class="fi-rr-spinner fi-spin"></i> </span></a>
                            </div>
                        </div>
                    </div>
                </section>
            </form>
        </div>
    </section>
</div>