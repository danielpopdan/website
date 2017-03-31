<div class="invoice-invoiced">  
  <div class="header">    
    <div class="invoice-header"><?php print render($content['invoice_header']); ?></div>
  </div>
  <div class="invoice-text"><?php print render($content['invoice_text']); ?></div>
  <div class="line-items">
    <div class="line-items-view"><?php print render($content['commerce_line_items']); ?></div>
  </div>
  <div class="footer"><?php print render($content['invoice_footer']); ?></div>
</div>

