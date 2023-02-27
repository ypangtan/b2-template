<?php echo view( 'admin/header_v2', [ 'header' => @$header ] );?>

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

        <?php echo view( 'admin/footer_v2' ); ?>
    </body>
</html>