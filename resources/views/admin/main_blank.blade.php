<?php echo view( 'admin/header', [ 'header' => @$header ] );?>

    <body>
        <!--start wrapper-->
        <div class="wrapper">

            <main class="page-content1">
                <?php echo view( $content, [ 'data' => @$data ] ); ?>
            </main>

            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST">
                @csrf
            </form>
            
        </div>

        <?php echo view( 'admin/footer' ); ?>
    </body>
</html>