<html>
    <table>
        <thead>
            <tr>
                <th style="font-size: 18px">{{translate('rashi_List')}}</th>
            </tr>
            <tr>

                <th>{{ translate('rashi_Analytics').' '.'-' }}</th>
                <th></th>
                <th>
                        {{translate('total_rashi').' '.'-'.' '.count($data['rashi'])}}
                    <br>
                        {{translate('inactive_rashi').' '.'-'.' '.$data['active']}}
                    <br>
                        {{translate('active_rashi').' '.'-'.' '.$data['inactive']}}
                </th>
            </tr>
            <tr>
                <th>{{translate('search_Criteria')}}-</th>
                <th></th>
                <th>  {{translate('search_Bar_Content').' '.'-'.' '.$data['search'] ?? 'N/A'}}</th>
            </tr>
            <tr>
                <td> {{translate('SL')}}	</td>
                <td> {{translate('rashi_Logo')}}</td>
                <td> {{translate('name')}}</td>
                <td> {{translate('status')}}	</td>
            </tr>
            @foreach ($data['rashi'] as $key=>$item)
                <tr>
                    <td> {{++$key}}	</td>
                    <td style="height: 70px"></td>
                    <td> {{$item['defaultName']}}</td>
                    <td> {{translate($item->status == 1 ? 'active' : 'inactive')}}</td>
                </tr>
            @endforeach
        </thead>
    </table>
</html>
