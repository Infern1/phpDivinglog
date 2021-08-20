<script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.2.0-beta.5/lightgallery.umd.min.js"
  integrity="sha512-Z3NNDbCAPzxSkBao3cU14RcJd6Ojs6YzNK/85sqpi85CI7yp6nfn17XG61yXFFHWbQNn75U4oT9b4E4dVrLshw=="
  crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.2.0-beta.5/plugins/thumbnail/lg-thumbnail.umd.min.js"
  integrity="sha512-v+/cnd6XTt28XV37rip+QRMB9OTYr90c3TxqNLLZZSH7cfoirS2N6bt9HRvlbyRnhco/vBK5pUCJdaIpS+fuhw=="
  crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.2.0-beta.5/plugins/zoom/lg-zoom.umd.min.js"
  integrity="sha512-mDd2qoh/FeskZ95fjdvG2vlbq37UCiwfEkyoap1Vub9g8rBdd3ETzVp+AlLwV+pGVdXZCFjFIbsHq5zHvEO4Ng=="
  crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript" src="{$app_path}/includes/misc.js"></script>



{* No images based tabs - variable width *}
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script type="text/javascript" src="{$app_path}/js/jquery.tools.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.15/datatables.min.js"></script>
{if isset($profile)}
  <script type="text/javascript" src="{$app_path}/includes/jqplot/jquery.jqplot.min.js"></script>
  <script type="text/javascript" src="{$app_path}/includes/jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
  <script type="text/javascript" src="{$app_path}/includes/jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
  <script type="text/javascript" src="{$app_path}/includes/jqplot/plugins/jqplot.highlighter.min.js"></script>
  <script type="text/javascript" src="{$app_path}/includes/jqplot/plugins/jqplot.cursor.min.js"></script>
{/if}
{if isset($piechart_display)}
  <script type="text/javascript" src="{$app_path}/includes/jqplot/jquery.jqplot.min.js"></script>
  <script type="text/javascript" src="{$app_path}/includes/jqplot/plugins/jqplot.pieRenderer.min.js"></script>
{/if}
<!-- tab styling -->
<link rel="stylesheet" type="text/css" href="{$app_path}/includes/tabs-no-images.css">