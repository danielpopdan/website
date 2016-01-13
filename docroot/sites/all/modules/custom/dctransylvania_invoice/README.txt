invoice_header = 

<p class="left-col">
<b>Company:</b> Reea SRL<br/><br/>
<b>VAT code:</b> RO10966500<br/>
<b>Register of Commerce NO.:</b> J26/628/1998<br/>
<b>Address:</b>Piata Republicii nr 41<br/>
Tirgu-Mures, 540110 <br/>
Mures, Romania <br/>
</p>

<p class="right-col">
<b>Buyer: </b>[#cumparator_name#]<br/><br/>
<b>Address:</b> [#adresa_facturare#]<br/>
<b>Phone:</b> -<br/><br/>
<b>Order number:</b> [#nr_comanda#]<br/>
</p>

<p class="right-col">
<b>Cumparator: </b>[#cumparator_name#]<br/><br/>
<b>Adresa facturare:</b> [#adresa_facturare#]<br/>
<b>Telefon:</b> -<br/><br/>
<b>Numar comanda:</b> [#nr_comanda#]<br/>
<b>Modalitate de plata:</b> [#modalitate_plata#]<br/>
</p>

/*============================================================================*/
Invoice_text=

<center>
<b class="invoice-title">Invoice</b> <br/>
<p class="invoice-subtitle"><b>Nr:</b> [#nr_comanda#] / <b>Date:</b>[#data_facturii#]</p>
</center>

/*============================================================================*/
Invoice_footer=

<table>
<tr>
<td class="sume-tva">

<b>Subtotal (no VAT) : </b>[#valoare_total_fara_tva#]<br/>
<b>VAT: </b>[#valoare_tva#]

</td>
<td>
<span class="total-factura"><b>Total: [#total_factura#]</b></span>
</td>
</tr>
</table><br/>
<center><b>Thanks for buying a ticket for the greatest Drupal event DrupalCamp Transylvania!</b><br/><br/>
http://drupaltransylvania.camp<br/>
<i><b>Need any help?</b> Contact us info@drupaltransylvania.camp</i>
</center>

/*============================================================================*/