<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PleskScan v0.1</title>
    <link rel="stylesheet" href="inc/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="inc/css/style.css"/>
    <script type="text/javascript" src="inc/js/jquery-1.10.1.min.js"></script>
    <script type="text/javascript" src="inc/js/main.js"></script>
</head>
<body>
    <div id="content">
        <div class="well">
            <h1 class="text-center">PleskScan v0.1</h1>
            <form class="form-inline">
                <input type="text" class="input-big" placeholder="www.page1.com, 192.168.0.1">
                <button disabled="disabled" type="submit" class="btn"><i class="icon-eye-open"></i> Check pages</button>
            </form>
            <table class="table table-striped">
                <tbody>
                <tr>
                    <th>Version</th>
                    <th>Host</th>
                    <th>Satus</th>
                    <th></th>
                </tr>

                </tbody>
            </table>

        </div>
    </div>
    <script type="text/javascript">
        // Disable button if there's no host given.
        $('form input').on('keyup', function(){
            var value = $(this).val();
            var button = $(this).parent().find('button');
            if(value == ''){
                button.attr('disabled', 'disabled');
            } else {
                button.removeAttr('disabled');
            }
        });
        // Prevent default form submit
        $('form').on('submit', function(e){
            e.preventDefault();
            var button = $(this).find('button');
            button.attr('disabled','disabled');
            var input = $(this).find('input');
            var value = input.val();
            input.val("");
            var hosts = value.split(","); // Split hosts based on the delimiter
            var table = $('table tbody');

            // Loop through the host list and perform Ajax request.
            hosts.forEach(function(element, index, array){
                var host = element.trim();

                $.ajax({
                    url: 'scan.php',
                    type: 'post',
                    data: {
                        host: host
                    }
                }).done(function(data){
                        if(data === 'error'){
                            var row = "<tr class='error'><td></td><td>" + host + "</td><td>Error</td><td class='text-right'><i class='icon-fire'></i></td></tr>";
                        } else if(data === 'none' || data === '' || data === null) {
                            var row = "<tr class='warning'><td></td><td>" + host + "</td><td>Not found</td><td class='text-right'><i class='icon-off'></i></td></tr>";
                        } else {
                            var obj = JSON.parse(data);
                            var row = "<tr class='success'><td>" + obj.title + "</td><td><a target='_blank' href='" + obj.host + "'>" + obj.host + "</a></td><td>" + obj.match + "</td><td class='text-right'><i class='icon-ok'></i></td></tr>";
                        }
                    button.removeAttr('disabled');
                    table.append(row);
                    }).error(function(){
                        button.removeAttr('disabled');
                        console.warn('damn it...');
                    });
            });

        });
    </script>
</body>
</html>