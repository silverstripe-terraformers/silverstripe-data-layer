<!doctype html>
<html lang="en">
<head>
    <%-- Required meta tags --%>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    $MetaTags(false)

    <% require themedCSS("dist/app") %>

    <title><% if $MetaTitle %>$MetaTitle<% else %>$Title<% end_if %> :: $SiteConfig.Title</title>
</head>
    <body data-pagetype="$ClassName">

        <div id="app">
            $Layout
        </div>

        <% require themedJavascript("dist/app") %>
    </body>
</html>
