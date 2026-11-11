<input type="hidden" name="h_data_productos_detraccion" id='h_data_productos_detraccion' value="{{json_encode($data_productos_detraccion)}}">

<div class="panel panel-border-color col-sm-12">
    <div class="form-group">			
        <div class="col-sm-12" style = ""> 
        <table class="table listatabledetalle ">
            <thead>
            <tr>
                <th class='center'>Tipo</th>
                <th class='center'>Código</th>
                <th>Producto</th>                   
                <th>Cantidad</th>
                <th>Precio (Midagri/Factura)</th>   
                <th>% Midagri</th>   
                <th>Detracción</th>   
            </tr>
            </thead>

            <tbody>
                @foreach($data_productos_detraccion as $index => $item)                
                    <tr class='fila_pedido'
                        data_detraccion = "{{$item['detraccion']}}">

                        <td class='center'>
                            <b style="padding-right: 4px;">{{$item['tipo_operacion']}}</b>
                        </td>

                        <td class='center'>
                            <b style="padding-right: 4px;">{{$item['transferencia_id']}}</b>
                        </td>

                        <td class="cell-detail relative" rowspan = "" > 
                            <span>{{$item['producto_nombre']}}</span>
                            <span class="cell-detail-description-producto">
                            </span>
                        </td>
                                
                        <td >{{number_format($item['cantidad'],4,'.',',')}}</td>

                        <td>{{number_format($item['precio_midragri'],4,'.',',')}}</td>	

                        <td>{{number_format($item['porcentaje_midragri'],4,'.',',')}}</td>	

                        <td>{{number_format($item['detraccion'],4,'.',',')}}</td>	

                    </tr>

                @endforeach

            </tbody>
            <tfooter>
            <tr>
                <th></th>
                <th></th>
            </tr>
            </tfooter>
        </table>	

        </div>
    </div>
</div>