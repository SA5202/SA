<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>輔仁大學貴重儀器預約系統</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="123.css">
    <link rel="icon" href="https://upload.wikimedia.org/wikipedia/zh/thumb/d/da/Fu_Jen_Catholic_University_logo.svg/1200px-Fu_Jen_Catholic_University_logo.svg.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@550&display=swap">
    <!--字體-->
    <link rel="icon" href="https://upload.wikimedia.org/wikipedia/zh/thumb/d/da/Fu_Jen_Catholic_University_logo.svg/1200px-Fu_Jen_Catholic_University_logo.svg.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@550&display=swap">
</head>

<body>
    <header>
        <div class="headergrid">
            <div style="margin-top: 15px; margin-left: 30px;">
                <h1 style="color: black;">輔仁大學貴重儀器預約系統</h1>
                <!-- Nav Item - User Information -->
            </div>
            <div style="margin-top: 15px;">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false" style="text-align: right;">
                    <span class="mr-2 d-none d-lg-inline text-gray-600 small" style="font-size: 14px;">使用者登入/註冊</span>
                    <i class="fa-regular fa-user"></i>
                </a>
                <!-- Dropdown - User Information -->
                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                    <div id="login-block" style="text-align: center;">
                        <button type="button" class="custom-button" onclick="window.location.href='登入.html'">登入</button><br>
                        <button type="button" class="custom-button" onclick="window.location.href='註冊.html'" style="margin-top: 10px;">註冊</button>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="wrapper">
        <div class="nav">
            <div class="sidebar">
                <ul><a href="首頁.html"><img class="img" src="https://cdn3.iconfinder.com/data/icons/fluent-regular-24px-vol-4/24/ic_fluent_home_24_regular-64.png"> 首頁</img></a></ul>
                <ul><a href="儀器預約現況.html"><img class="img" src="https://cdn2.iconfinder.com/data/icons/school-set-5/512/6-64.png"> 儀器預約現況</img></a></ul>
                <ul><a href="儀器預約使用.html"><img class="img" src="https://cdn4.iconfinder.com/data/icons/user-interface-937/24/20.clock_time_watch_date_ui-64.png"> 預約儀器使用</img></a></ul>
                <ul><a href="繳費系統.html"><img class="img" src="https://cdn0.iconfinder.com/data/icons/finance-business-02/32/Payment-64.png"></img> 繳費系統</a></ul>
                <ul><a href="使用規則與管理辦法.html"><img class="img" src="https://cdn2.iconfinder.com/data/icons/legal-services-icostory-black-and-white/64/button-legal_document-list-goals-checklist-64.png"> 使用規則與管理辦法</img></a></ul>
                <ul><a href="校內資源.html"><img class="img" src="https://cdn0.iconfinder.com/data/icons/miscellaneous-4-bold/64/atomizing_nuclear_atomic_core_connect-64.png"> 校內資源</img></a></ul>
                <ul><a href="後臺管理.html"><img class="img" src="https://cdn1.iconfinder.com/data/icons/carbon-design-system-vol-3/32/cloud--service-management-64.png"></img> 後臺管理</a></ul>
                <ul>
                    <div class="dropdown">
                        <a href="#">
                            <img class="img1" src="https://cdn4.iconfinder.com/data/icons/standard-free-icons/139/Setting01-64.png" alt="建置中">
                            建置中
                            <div class="triangle"></div>
                        </a>
                        <div class="dropdown-content">
                            <ul>
                                <li><a href="#"><img class="img1" src="https://cdn3.iconfinder.com/data/icons/linecons-free-vector-icons-pack/32/mail-64.png"> 聯絡我們</a></li>
                                <li><a href="#"><img class="img1" src="https://cdn3.iconfinder.com/data/icons/fluent-regular-24px-vol-5/24/ic_fluent_people_community_24_regular-64.png"> 單位成員</a></li>
                                <li><a href="#"><img class="img1" src="https://cdn1.iconfinder.com/data/icons/ionicons-sharp-vol-1/512/list-sharp-64.png"> 中心介紹</a></li>
                                <li><a href="#"><img class="img1" src="https://cdn3.iconfinder.com/data/icons/user-inteface-17/24/edit_pencil_write_paper_file_document_study_copywriting_writing-64.png"> 儀器線上學習</a></li>
                                <li><a href="#"><img class="img1" src="https://cdn4.iconfinder.com/data/icons/business-solid-the-capitalism/64/Efficacy_researching-64.png"> 檢驗委託程序</a></li>
                                <li><a href="#"><img class="img1" src="https://cdn1.iconfinder.com/data/icons/bootstrap-vol-3/16/newspaper-64.png"> 研究資源組-電子報</a></li>
                            </ul>
                        </div>
                    </div>
                </ul>
            </div>
        </div>
        <div class="main">
            <div class="wrapper1">
                <div></div>
                <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner" style="margin: 40px;">
                        <div class="carousel-item active">
                            <img src="https://www.fju.edu.tw/showImg/focus/focus1684.jpg">
                        </div>
                        <div class="carousel-item">
                            <img src="https://www.fju.edu.tw/showImg/focus/focus2293.jpg">
                        </div>
                        <div class="carousel-item">
                            <img src="https://www.fju.edu.tw/showImg/focus/focus2236.jpg">
                        </div>
                        <div class="carousel-item">
                            <img src="https://www.fju.edu.tw/showImg/focus/focus1495.jpg">
                        </div>
                        <div class="carousel-item">
                            <img src="https://newspeople.com.tw/wp-content/uploads/20160321154119_92.jpg">
                        </div>
                        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                </div>
                <div></div>
            </div>
            <div>
                <h2 style="margin-top: 20px; margin-left: 30px;">// 最新消息 //</h2>
                <div class="news">
                    <p><a href="#">2024-06-14 丨 Web 網頁設計期末作業繳交期限將至，竟有人尚未動工！？</a></p>
                    <p><a href="#">2024-06-13 丨 進階程式設計一片倒？Java考題難度太高？</a></p>
                    <p><a href="#">2024-06-12 丨 會計學期末考成績出爐，資管一乙哀鴻遍野！！</a></p>
                    <p><a href="#">2024-06-11 丨 自主扣考會計學？沈姓同學親上火線解釋原由！</a></p>
                    <p><a href="#">2024-06-10 丨 端午節到來，接下來一週臺灣人三餐不必發愁</a></p>
                </div>
            </div>
        </div>
    </div>


    <footer>
        <div class="footergrid">
            <div></div>
            <div style="margin-right:20px">2024 © 輔仁大學 研究資源整合發展中心
                <br>建議使用 Chrome / Safari / Firefox瀏覽
            </div>

            <div style="margin-left: 50%;margin-top: 15px;"><a href="#top" id="gototop"><span>返回頂端</span></a></div>

            <div></div>
        </div>
    </footer>
</body>

</html>