<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	{!! Html::style('public/css/excel/excel.css') !!}

    <!-- titulo -->
    <table>
        <tr>
            <td class = 'subtitulos' colspan="4">{{$empresa}} - {{$centro}}</td>                    
        </tr>

        <tr>
            <th class= 'tabladp'>Numero Mes</th>
            <th class= 'tabladp'>Cuenta</th>
            <th class= 'tabladp'>Codigo PLE</th>              
            <th class= 'tabladp'>Tipo de Activo</th> 
            <th class= 'tabladp'>Descripcion</th>
            <th class= 'tabladp'>Factura</th>
            <th class= 'tabladp'>Razon Social</th>
            <th class= 'tabladp'>Clasificacion de Activos</th>
            <th class= 'tabladp'>Saldo Inicial</th>
            <th class= 'tabladp'>Mes Adquisiciones</th>
            <th class= 'tabladp'>Adquisiciones</th>
            <th class= 'tabladp'>Retiros</th>
            <th class= 'tabladp'>Base de Calculo</th>
            <th class= 'tabladp'>Fecha de Adq</th>
            <th class= 'tabladp'>Fecha de Inicio</th>
            <th class= 'tabladp'>Tasa</th>
            <th class= 'tabladp'>Deprec Acumulada al 2021</th>
            <th class= 'tabladp'>Dias</th>
            <th class= 'tabladp'>Por depreciar</th>
            <th class= 'tabladp'>Condicion</th>
            @for ($i=1; $i<=$mes; $i++)
                <th class= 'tabladp'>{{$meses_esp[$i]}}</th>   
            @endfor
            <th class= 'tabladp'>Deprec 2022</th>
            <th class= 'tabladp'>Deprec Acumulada al 2022</th>
            <th class= 'tabladp'>Depreciacion de Retiros/bajas</th>
            <th class= 'tabladp'>Saldo Final Depreciacion</th>
            <th class= 'tabladp'>Saldo a depreciar 2022</th>            
        </tr>

            @foreach($catalogo as $activo) 

                    @if(count($catalogo)>0) 
                        
                            <tr>
                                <td>{{date("m",strtotime($activo['fecha_registro']))}}</td>
                                <td>{{$activo['cuenta_activo']}}</td>
                                <td>{{$activo['item_ple']}}</td>
                                <td>{{$activo['tipo_activo']}}</td>
                                <td>{{$activo['nombre']}}</td>
                                <td>{{$activo['factura']}}</td>
                                <td>{{$activo['empresa']}}</td>
                                <td>{{$activo['categoria']}}</td>
                                <td>S/. {{number_format($activo['saldo_inicial'], 4, '.', ',')}}</td>
                                <td>{{$activo['mes'][$activo['mes_adquisicion']]}}</td>
                                <td>S/. {{number_format($activo['adquisicion'], 4, '.', ',')}}</td>
                                <td></td>
                                <td>S/. {{number_format($activo['base_de_calculo'], 4, '.', ',')}}</td>                        
                                <td>{{date("d/m/Y",strtotime($activo['fecha_adquisicion']))}}</td>                        
                                <td>{{$activo['fecha_inicio_depreciacion']}}</td>
                                <td>% {{number_format($activo['tasa_depreciacion'], 4, '.', ',')}}</td>
                                <td>S/. {{number_format($activo['depreciacion_acumulada_anio_anterior'], 4, '.', ',')}}</td>
                                <td>{{$activo['dias']}}</td>
                                <td>S/. {{number_format($activo['por_depreciar'], 4, '.', ',')}}</td>
                                <td>{{$activo['condicion']}}</td>
                                @for ($i=1; $i<=$mes; $i++)                         
                                    <td>
                                        @isset($activo['meses'][$i])
                                        {{number_format($activo['meses'][$i], 4, '.', ',')}}
                                        @endisset
                                    </td>
                                @endfor
                                <td>S/. {{number_format($activo['depreciacion_anio'], 4, '.', ',')}}</td>
                                <td>S/. {{number_format($activo['depreciacion_acumulada_total'], 4, '.', ',')}}</td>
                                <td></td>
                                <td>S/. {{number_format($activo['saldo_final_depreciacion'], 4, '.', ',')}}</td>
                                <td>S/. {{number_format($activo['saldo_a_depreciar_anio'], 4, '.', ',')}}</td>
                            </tr>
                        
                    @else 
                        <tr>
                        <td></td>   
                        </tr>
                    @endif
                

            @endforeach


    </table>
</html>
