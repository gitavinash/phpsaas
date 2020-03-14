<link href="https://fonts.googleapis.com/css?family=Eczar:400,700&display=swap" rel="stylesheet">

<style amp-boilerplate>
    body {
        -webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;
        -moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;
        -ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;
        animation:-amp-start 8s steps(1,end) 0s 1 normal both
    }
        
    @-webkit-keyframes -amp-start{
        from {
            visibility:hidden
        }
        to {
            visibility:visible
        }
    }
        
    @-moz-keyframes -amp-start {
        from {
            visibility:hidden
        }
        to {
            visibility:visible
        }
    }
        
    @-ms-keyframes -amp-start {
        from {
            visibility:hidden
        }
        to {
            visibility:visible
        }
    }
        
    @-o-keyframes -amp-start {
        from {
            visibility:hidden
        }
        to {
            visibility:visible
        }
    }

    @keyframes -amp-start{
        from {
            visibility:hidden
        }
        to {
            visibility:visible
        }
    }
</style>

<noscript>
    <style amp-boilerplate>
        
        body {
            -webkit-animation:none;
            -moz-animation:none;
            -ms-animation:none;
            animation:none
        }

    </style>
</noscript>

<style amp-custom>
    
    /* Theme css here */

    body {
        font-family: 'Eczar', serif;
    }

    header {
        float: left;
        width: 100%;
        box-shadow: 0px 1px 5px 0px #d8d8d8;
        position: fixed;
        z-index: 9;
        box-sizing: border-box;
        background: #fff;
    }

    .logo-box {
        float: left;
        width: 100%;
        box-shadow: 0px 1px 5px 0px #d8d8d8;
        z-index: 9;
        padding: 2px 10px 2px 10px;
        box-sizing: border-box;
        background: #fff;
    }

    .logo-box .logo {
        float: left;
        width: 40%;
    }

    .logo-box .sidebar {
        float: right;
        width: 60%;
    }

    /** Start Global Styling */

    .bgD01F29 {
        background: #D01F29;
    }

    .no-padding {
        padding: 0;
    }

    .amp-carousel-button {
        opacity: 1;
    }

    .ad-box-small-rectangular {
        float: left;
        width: 100%;
        height: 100px;
        padding: 10px 0px;
        align-items: center;
        display: flex;
        background: #f5f5f5;
        justify-content: center;
    }

    .ad-box {
        float: left;
        width: 100%;
        height: 250px;
        padding: 10px 0px;
        align-items: center;
        display: flex;
        background: #f5f5f5;
        justify-content: center;
    }

    .in-article-ad-box {
        float: left;
        width: 100%;
        height: 250px;
        padding: 10px 0px;
        align-items: center;
        display: flex;
        background: #f5f5f5;
        justify-content: center;
        margin-bottom: 10px;
    }

    p, span, i, ul, li, h1, h2, h3, h4, h5, h6, a {
        padding: 0;
        margin: 0 auto;
        text-decoration: none;
    }

    h1.category-title {
        float: left;
        padding: 5px 10px 0px 10px;
        color: #fff;
        box-sizing: border-box;
        text-shadow: 1px 0 0 #000, -1px 0 0 #000, 0 1px 0 #000, 0 -1px 0 #000, 1px 1px #000, -1px -1px 0 #000, 1px -1px 0 #000, -1px 1px 0 #000;
        font-family: eczar,serif;
        text-transform: capitalize;
    }

    /** End Gloabl Styling */

    .other-option {
        float: right;
        padding: 0;
        margin: 0 auto;
    }

    .other-option ul {
        float: right;
        list-style: none;
        margin: 0 auto;
        padding: 0;
    }

    .other-option ul li {
        float: left;
    }

    .other-option ul li a i {
        font-size: 30px;
        margin: 4px 10px;
        float: left;
        color: #525252;
        line-height: 50px;
        padding: 0px 5px;
        border-radius: 4px;
    }

    .other-option ul li amp-img {
        margin: 8px 5px;
        float: left;
        line-height: 50px;
        padding: 0px 5px;
    }

    .menu-box {
        float: left;
        width: 100%;
        overflow: auto;
        box-sizing: border-box;
        box-shadow: -1px 2px 4px 0px #a90000;
    }

    .menu-box nav {
        float: left;
        width: 100%;
    }

    .menu-box nav ul {
        float: left;
        list-style: none;
        width: 100%;
        margin: 0;
        overflow: auto;
        white-space: nowrap;
        background: #D01F29;
        padding: 0px 0px 0px 40px;
    }

    .menu-box nav ul li {
        display: inline-block;
    }

    .menu-box nav ul li:first-child {
        position: absolute;
        display: inline-block;
        background: #D01F29;
        margin-left: -40px;
        padding: 1px 0px 0px 0px;
    }

    .menu-box nav ul li a {
        display: inline-block;
        color: #fff;
        text-decoration: none;
        line-height: 18px;
        font-weight: 500;
        font-size: 14px;
        font-family: 'Eczar', serif;
        padding: 5px 15px 5px 15px;
    }

    .menu-box nav ul li a.active {
        border-bottom: 1px solid #fff;
        padding: 5px 15px 4px 15px;
    }

    .menu-box nav ul li a:hover {
        color: #d4d4d4;
    }

    .menu-box nav ul li ul {
        float: left;
        display: none;
        position: absolute;
        margin-top: 60px;
        background: #fff;
        z-index: 9999;
    }

    .menu-box nav ul li:hover ul {
        display: block;
        float: left;
        width: 150px;
        padding: 0px 25px;
        text-align: left;
    }

    .menu-box nav ul li ul li {
        float: left;
        width: 100%;
        position: relative;
        border-bottom: 1px solid #f4f4f4;
    }

    .menu-box nav ul li ul li a {
        float: left;
        width: 100%;
        line-height: 45px;
        padding: 0px;
    }

    .menu-box nav ul li ul li:last-child {
        border-bottom: 0px;
    }

    main {
        float: left;
        width: 100%;
        margin-top: 95px;
        background: #fff;
    }

    section {
        float: left;
        width: 100%;
        padding: 5px 0px;
        background: #fff;
    }

    .slider {
        float: left;
        width: 100%;
        background: #D01F29;
        margin-top: -30px;
        padding-top: 20px;
        box-sizing: border-box;
        padding-bottom: 15px;
    }

    .slider .slide {
        float: left;
        width: 100%;
        padding: 10px 10px 20px 10px;
        box-sizing: border-box;
        border-radius: 4px;
    }

    .slider .slide a {
        color: #fff;
        box-sizing: border-box;
        text-shadow: 1px 0 0 #000, -1px 0 0 #000, 0 1px 0 #000, 0 -1px 0 #000, 1px 1px #000, -1px -1px 0 #000, 1px -1px 0 #000, -1px 1px 0 #000;
        font-family: eczar,serif;
    }
    
    .slide .img-box {
        float: left;
        width: 100%;
        border-radius: 4px;
    }

    .slide amp-img {
        float: left;
        width: 100%;
        border-radius: 4px;
    }

    .slide .text-box {
        position: absolute;
        bottom: 0;
        float: left;
        width: 95%;
        border-radius: 4px;
    }

    .slide .text-box amp-text-fit {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        float: left;
        width: 100%;
        margin: 0 auto;
    }

    .categoryBox {
        float: left;
        width: 100%;
        padding: 0px 10px;
        box-sizing: border-box;
    }

    .categoryBox .post-box {
        float: left;
        width: 100%;
        margin-bottom: 10px;
        padding-bottom: 10px;
        box-sizing: border-box;
        box-shadow: 0px 1px 0px 0px #e0dddd;
    }

    .categoryBox .post-box:last-child {
        box-shadow: none;
        padding-bottom: 0px;
    }

    .categoryBox .post-box:nth-child(6n+5) {
        box-shadow: none;
        padding-bottom: 0px;
    }

    .categoryBox .text-box {
        float: left;
        width: 70%;
    }

    .categoryBox .text-box span {
        float: left;
        width: 100%;
        font-size: 12px;
        font-weight: 600;
        color: #D01F29;
        text-transform: capitalize;
    }

    .categoryBox .text-box h2 {
        float: left;
        width: 100%;
        font-size: 16px;
        color: #333;
        padding: 0px 5px 0px 0px;
        box-sizing: border-box;
        text-align: left;
    }

    .categoryBox .img-box {
        float: right;
        width: 30%;
    }

    .categoryBox .img-box amp-img {
        float: left;
        width: 100%;
        border-radius: 4px;
    }

    .categoryBox .img-box amp-img.live {
        float: right;
        height: 13px;
        margin-bottom: 5px;
        width: auto;
        border-radius: 4px;
    }

    .article {
        float: left;
        width: 100%;
        padding: 0px 10px;
        box-sizing: border-box;
    }

    .article h1 {
        float: left;
        width: 100%;
        color: #000;
        box-sizing: border-box;
        font-family: eczar,serif;
        font-size: 18px;
        padding-top: 20px;
    }

    .article .feature-img {
        float: left;
        width: 100%;
        padding: 10px 0px 20px 0px;
    }

    .article .feature-img amp-img {
        float: left;
        width: 100%;
    }

    .article .content {
        float: left;
        width: 100%;
    }

    .article .content amp-img {
        float: left;
        width: 100%;
    }

    .article .content amp-instagram {
        float: left;
        width: 100%;
        display: block;
    }

    .article .content amp-twitter {
        float: left;
        width: 100%;
    }

    .article .content amp-youtube {
        float: left;
        width: 100%;
    }

    .article .content p {
        float: left;
        width: 100%;
        line-height: 30px;
        font-size: 16px;
        color: #333;
        text-align: justify;
        margin: 10px 0px;
    }

    .article .content h2 {
        width: 100%;
        font-size: 14px;
        letter-spacing: 1px;
        font-weight: 300;
        text-decoration: none;
        color: #00f;
    }

    .article .content h2::before {
        float: left;
        content: "ये भी पढ़ें:";
        padding-right: 20px;
        color: rgb(0, 0, 0);
        font-weight: 700;
    }

    .article .content h1 {
        width: 100%;
        font-size: 14px;
        letter-spacing: 1px;
        font-weight: 300;
        text-decoration: none;
        color: #00f;
    }

    .article .content h1::before {
        float: left;
        font-size: 14px;
        content: "ये भी पढ़ें:";
        padding-right: 20px;
        color: rgb(0, 0, 0);
        font-weight: 700;
    }

    .article .tags {
        float: left;
        width: 100%;
    }

    .article .tags a {
        float: left;
        color: #fff;
        background: #D01F29;
        font-size: 14px;
        line-height: 28px;
        transition: background 0.3s;
        margin: 0px 10px 5px 0px;
        padding: 0px 10px;
        text-transform: capitalize;
        border-radius: 10px;
    }

</style>