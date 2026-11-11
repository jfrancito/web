<style>
    .premium-table-container {
        width: 100%;
        overflow-x: auto;
        margin-bottom: 20px;
        background: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    .premium-table {
        width: 100%;
        min-width: max-content;
        margin-bottom: 0;
        border-collapse: collapse !important;
    }

    .premium-table th, .premium-table td {
        padding: 8px 12px !important;
        vertical-align: middle !important;
        border: 1px solid #d1d5db !important; /* Bordes claros para cada celda */
        color: #374151;
        font-size: 13px;
    }
    
    .premium-table td.center, .premium-table th.center {
        text-align: center !important;
    }
    
    /* Estilos para el encabezado o primera fila */
    .premium-table tr:first-child th,
    .premium-table tr:first-child td,
    .premium-table tr:nth-child(2) td,
    .premium-table tr:nth-child(3) td {
        background-color: #f3f4f6 !important;
        font-weight: 700;
        color: #111827;
        text-transform: uppercase;
        font-size: 12px;
    }

    .premium-table tbody tr:hover td {
        background-color: #e5e7eb !important;
    }

    .premium-table td.negrita {
        font-weight: bold !important;
        color: #000000;
        background-color: #f9fafb;
    }
</style>

<div style="margin-bottom: 15px; text-align: right;">
    <button onclick="exportTableToExcel('table-comisiones', 'Cuadro de Comisiones')" class="btn btn-space btn-success" style="font-weight: bold;">
        <i class="mdi mdi-file-excel"></i> Exportar a Excel
    </button>
</div>

<div class="scroll_text_horizontal_padding premium-table-container"> 
    <div>
        <table id="table-comisiones" class="table premium-table">  
          <tbody>
           @foreach($cuadrocomisiones as $index=>$item)
              	<tr>
            			@for ($i = 0; $i < $item['cantidadarray']; $i++)

                    @php $colspan    =   '0'; @endphp
                    @php $creartd    =   '0'; @endphp
                    @php $clscenter  =   '0'; @endphp
                    @php $rowspan    =   '0'; @endphp
                    @php $creartdro  =   '0'; @endphp
                    @php $negrita    =   ''; @endphp


                    @if(isset($item['colspan'.$i]))
                      @if($item['colspan'.$i] != '-1') 
                        @php $colspan   =  $item['colspan'.$i]; @endphp
                        @php $clscenter   =  'center'; @endphp
                      @endif
                    @endif

                    @if(isset($item['colspan'.$i]) && $item['colspan'.$i] == '-1')
                      @php $creartd   =  1; @endphp
                    @endif


                    @if(isset($item['rowspan'.$i]))
                      @php $clscenter   =  'ddddd'; @endphp
                      @if($item['rowspan'.$i] != '-1') 
                        @php $rowspan   =  $item['rowspan'.$i]; @endphp
                        @php $clscenter   =  'center'; @endphp
                      @endif

                    @endif
                    @if(isset($item['rowspan'.$i]) && $item['rowspan'.$i] == '-1')
                      @php $creartdro   =  1; @endphp
                    @endif

                    @if(isset($item['negrita'.$i]))
                        @php $negrita   =  $item['negrita'.$i]; @endphp
                    @endif   

                    @if(isset($item['center'.$i]))
                        @php $clscenter   =  $item['center'.$i]; @endphp
                    @endif 


                    @if(isset($item['colspan'.$i]))
                      @if($creartd == 0) 
                        <td colspan="{{$colspan}}" class="{{$clscenter}} {{$negrita}}" >{{$item['item'.$i]}}</td>
                      @endif
                    @else

                      @if(isset($item['rowspan'.$i]))
                        @if($creartdro == 0 && $i == 0) 
                          <td rowspan="{{$rowspan}}" class="{{$clscenter}} {{$negrita}}" >{{$item['item'.$i]}}</td>
                        @endif
                      @else
                          <td class='{{$clscenter}} {{$negrita}}'>{{$item['item'.$i]}}</td>
                      @endif
                    @endif  
            			 
            			@endfor
              	</tr>                    
            @endforeach
          </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
  function exportTableToExcel(tableID, filename = ''){
      var downloadLink;
      var dataType = 'application/vnd.ms-excel';
      
      // Clonar la tabla para no modificar la vista original
      var originalTable = document.getElementById(tableID);
      var clonedTable = originalTable.cloneNode(true);
      
      // Aplicar estilos para que Excel los reconozca
      clonedTable.setAttribute('border', '1');
      clonedTable.style.borderCollapse = 'collapse';
      clonedTable.style.width = '100%';
      
      var rows = clonedTable.getElementsByTagName('tr');
      for (var i = 0; i < rows.length; i++) {
          var cells = rows[i].children;
          for (var j = 0; j < cells.length; j++) {
              var cell = cells[j];
              cell.style.border = '1px solid #000000';
              cell.style.padding = '5px';
              
              // Si es parte del encabezado (primeras 3 filas) o tiene clase 'negrita'
              if (i < 3 || cell.className.indexOf('negrita') > -1) {
                  cell.style.fontWeight = 'bold';
                  cell.style.backgroundColor = '#D9D9D9';
                  cell.style.color = '#000000';
              }
              
              // Si tiene clase 'center'
              if (cell.className.indexOf('center') > -1) {
                  cell.style.textAlign = 'center';
              }
          }
      }

      // Reemplazar espacios y tildes para evitar problemas de codificación al exportar
      var tableHTML = clonedTable.outerHTML.replace(/ /g, '%20');
      
      // Agregar meta tag para soportar tildes y caracteres especiales en excel (UTF-8)
      var htmlTemplate = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><meta charset="UTF-8"><style>table { font-family: Arial, sans-serif; }</style></head><body>' + clonedTable.outerHTML + '</body></html>';
      
      filename = filename ? filename + '.xls' : 'cuadro_comisiones.xls';
  
      // Crear blob
      var blob = new Blob(['\ufeff', htmlTemplate], {
          type: dataType
      });
      
      if(navigator.msSaveOrOpenBlob){
          navigator.msSaveOrOpenBlob(blob, filename);
      }else{
          downloadLink = document.createElement("a");
          document.body.appendChild(downloadLink);
          
          if(window.URL && window.URL.createObjectURL){
              downloadLink.href = window.URL.createObjectURL(blob);
          } else {
              downloadLink.href = 'data:' + dataType + ', ' + encodeURIComponent(htmlTemplate);
          }
          
          downloadLink.download = filename;
          downloadLink.click();
          document.body.removeChild(downloadLink);
      }
  }
</script>

@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){
       App.dataTables();
    });
  </script> 
@endif

