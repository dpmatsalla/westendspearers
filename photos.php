<!DOCTYPE html>
<html lang='en'>
<head>
    <title>Spearers Photo Gallery</title>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'/>
    <meta name='keywords' content='West End, Spearers, kayak, kayaking, canoe, surfski, ocean ski, paddling, photo, gallery, Brisbane, Queensland, Australia'/>
    <meta name='description' content='This collection of photos spans the past 20 years of paddling on the Brisbane river and good camaraderie'/>
    <meta name='author' content='Devon Matsalla' />
    <meta property="og:title" content="Spearers Photo Gallery" />
    <meta property="og:description" content="This collection of photos spans the past 20 years of paddling on the Brisbane river and good camaraderie" />
    <meta property="og:image" content="https://westendspearers.com.au/images/westendspearers.jpg?v=1" />
    <meta property="og:url" content="https://westendspearers.com.au/photos.php" />
    <meta property="og:type" content="website" />
    <meta name="twitter:card" content="summary_large_image" />
    <link rel='icon' type='image/x-icon' href='/images/spearers.ico' />
    <link rel='canonical' href='https://westendspearers.com.au/photos.php' />

    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js'></script>
    <link rel='stylesheet' type='text/css' href='styles.css?v=13'>

    <!-- Google tag (gtag.js) -->
    <script async src='https://www.googletagmanager.com/gtag/js?id=G-P87WWP1YGX'></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-P87WWP1YGX');
    </script>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-NBBRQ6H3');</script>
    <!-- End Google Tag Manager -->
    
</head>
<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src='https://www.googletagmanager.com/ns.html?id=GTM-NBBRQ6H3'
    height='0' width='0' style='display:none;visibility:hidden'></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <header class='darkbg'>
        <nav class='navbar navbar-expand-lg navbar-dark'>
            <div class='container'>
                <a class='navbar-brand' href='/'><img src='/images/SpearersLogoClearDark.png' alt='Logo' width='250'></a>
                <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#collapsibleNavbar'>
                    <span class='navbar-toggler-icon'></span>
                </button>
                <div class='collapse navbar-collapse' id='collapsibleNavbar'>
                    <ul class='navbar-nav'>
                        <li class='nav-item'><a class='nav-link' href='/'>Home</a></li>
                        <li class='nav-item'><a class='nav-link' href='/#tides'>Tides</a></li>
                        <li class='nav-item'><a class='nav-link' href='/#calendarsection'>Calendar</a></li>
                        <li class='nav-item dropdown'>
                            <a class='nav-link dropdown-toggle' href='cheese.php' role='button' data-bs-toggle='dropdown'>Cheese</a>
                            <ul class='dropdown-menu dropdown-menu-dark'>
                                <li><a class='dropdown-item' href='cheese.php'>The Cheese</a></li>
                                <li><a class='dropdown-item' href='cheese.php#athletes'>The Spearers</a></li>
                                <li><a class='dropdown-item' href='cheese.php#commandments'>Commandments</a></li>
                                <li><a class='dropdown-item' href='cheese.php#rules'>Rules</a></li>
                            </ul>
                        </li>
                        <li class='nav-item'><a class='nav-link' href='photos.php#home'>Photo Gallery</a></li>
                        <li class='nav-item dropdown'>
                            <a class='nav-link dropdown-toggle' href='AGM.php' role='button' data-bs-toggle='dropdown'>AGM</a>
                            <ul class='dropdown-menu dropdown-menu-dark'>
                                <li><a class='dropdown-item' href='AGM.php#'>AGM</a></li>
                                <li><a class='dropdown-item' href='AGM.php#tshirts'>T-shirts</a></li>
                                <li><a class='dropdown-item' href='AGM.php#gallery'>Photo Gallery</a></li>
                                <li><a class='dropdown-item' href='AGM.php#mapsection'>Map</a></li>
                            </ul>
                        </li>
                        <li class='nav-item'><a class='nav-link' href='/#mapsection'>Map</a></li>
                        <li class='nav-item'><a class='nav-link' href='/#contact'>Contact</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class='container'>
            <img src='/images/spearers-wideangle.jpg' alt='spearers-wideangle' width='100%'>
        </div> -->
    </header>

    <main>
        <section id='home' class='darkbg'>
            <div class='container p-3'>
                <h1>Photo Gallery</h1>
                <p>20 years of great paddling, great camaraderie and great coffee</p>
            </div>
        </section>

        <section id='gallery' class='darkbg'>
            <div class='container p-3'>
                <div class="gallery" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));">
                    <?php
                    $photoDirectory = '/home/westends/public_html/photos'; // photo directory
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                    if (is_dir($photoDirectory)) {
                        $files = scandir($photoDirectory);
                        //print_r ($files);
                        foreach ($files as $file) {
                            $fileInfo = pathinfo($file);
                            $fileExtension = strtolower($fileInfo['extension']);
                            if (in_array($fileExtension, $allowedExtensions)) {
                                echo '<div class="photo"><img src="/photos/' . $file . '" alt="' . $file . '"></div>';
                            }
                        }
                    } else {
                        echo 'The specified directory does not exist.';
                    }
                    ?>
                </div>
            </div>
        </section>

        <section id='gallery' class='graybg'>
            <div class='container p-3'>
                <form action="" method="post" enctype="multipart/form-data">
                    <h2>Upload a Photo</h2>
                    <p>Reduce photo to less than 500 KB before uploading, as space is limited</p>
                    <input type="file" name="fileToUpload" id="fileToUpload" accept=".jpg, .jpeg, .png, .gif">
                    <input type="submit" value="Upload" name="uploadBtn">
                </form>
            
                <?php
                if (isset($_POST['uploadBtn'])) {
                    $targetDirectory = '/home/westends/public_html/photos'; // photo directory
                    $allowedFileSize = 500 * 1024; // 500 KB
            
                    if (is_dir($targetDirectory)) {
                        $targetFile = $targetDirectory . '/' . basename($_FILES['fileToUpload']['name']);
                        $fileExtension = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            
                        if (in_array($fileExtension, $allowedExtensions) && $_FILES['fileToUpload']['size'] <= $allowedFileSize) {
                            if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $targetFile)) {
                                echo 'File uploaded successfully.';
                                header("Refresh:0");
                            } else {
                                echo 'Error uploading the file.';
                            }
                        } else {
                            echo 'Invalid file format or size exceeds 500 KB.';
                        }
                    } else {
                        echo 'The specified directory does not exist.';
                    }
                }
                ?>
                <br>
            </div>
        </section>
    </main>

    <footer class='darkbg'>
        <div class='container' style='padding:0;'>
            <table class='calendar' style='table-layout: auto;'><thead><tr>
                <th><a href='/#home'>Home</a> </th>
                <th><a href='/#tides'>Tides</a> </th>
                <th><a href='cheese.php#cheese'>Cheese</a> </th>
                <th><a href='photos.php#home'>Photos</a> </th>
                <th><a href='AGM.php#AGM'>AGM</a></th>
                <th><a href='/#mapsection'>Map</a> </th>
                <th><a href='/#contact'>Contact</a> </th>
            </tr></thead></table>
            <br>
            <div class='row'>
                <div class='col'>
                    <a class='navbar-brand' href='/'><img src='/images/SpearersLogoClearDark.png' class='d-block' alt='Logo' width='200'></a>
                </div>
                <div class='col'>
                    <p>&copy; 2023 Spearers. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>
    
</body>
</html>
