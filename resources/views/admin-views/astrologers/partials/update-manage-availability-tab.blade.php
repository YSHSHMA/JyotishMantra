<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <h4>Sunday</h4>
            <table class="table table-borderless table-hover" id="sunday-update-dynamic-field">
                <tr>
                    <td class="pb-0">
                        <label for="" class="form-label">From Time</label>
                    </td>
                    <td class="pb-0">
                        <label for="" class="form-label">To Time</label>
                    </td>
                </tr>

                @if (empty(json_decode($availability['sunday'])))
                    <tr>
                        <td class="pt-0" style="width: 45%">
                            <input type="time" name="sunday_from[]" class="form-control" />
                        </td>
                        <td class="pt-0" style="width: 45%">
                            <input type="time" name="sunday_to[]" class="form-control" />
                        </td>
                        <td class="pt-0" style="width: 10%;">
                            <button type="button" id="sunday-update-add" class="btn btn-primary"><i>+</i></button>
                        </td>
                    </tr>
                @else
                    @foreach (json_decode($availability['sunday']) as $sundayKey => $sunday)
                        <tr id="sunday-update-row{{ $sundayKey + 1 }}">
                            <td class="pt-0" style="width: 45%">
                                <input type="time" name="sunday_from[]" class="form-control"
                                    value="{{ explode('-', $sunday)[0] }}" />
                            </td>
                            <td class="pt-0" style="width: 45%">
                                <input type="time" name="sunday_to[]" class="form-control"
                                    value="{{ explode('-', $sunday)[1] }}" />
                            </td>
                            @if ($loop->first)
                                <td class="pt-0" style="width: 10%;">
                                    <button type="button" id="sunday-update-add"
                                        class="btn btn-primary"><i>+</i></button>
                                </td>
                            @else
                                <td class="pt-0" style="width: 10%;">
                                    <button type="button" id="{{ $sundayKey + 1 }}"
                                        class="btn btn-danger sunday-update-btn-remove"><i>x</i></button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @endif
            </table>

            <h4>Monday</h4>
            <table class="table table-borderless table-hover" id="monday-update-dynamic-field">
                <tr>
                    <td class="pb-0">
                        <label for="" class="form-label">From Time</label>
                    </td>
                    <td class="pb-0">
                        <label for="" class="form-label">To Time</label>
                    </td>
                </tr>
                @if (empty(json_decode($availability['monday'])))
                    <tr>
                        <td class="pt-0" style="width: 45%">
                            <input type="time" name="monday_from[]" class="form-control" />
                        </td>
                        <td class="pt-0" style="width: 45%">
                            <input type="time" name="monday_to[]" class="form-control" />
                        </td>
                        <td class="pt-0" style="width: 10%;">
                            <button type="button" id="monday-update-add" class="btn btn-primary"><i>+</i></button>
                        </td>
                    </tr>
                @else
                    @foreach (json_decode($availability['monday']) as $mondayKey => $monday)
                        <tr id="monday-update-row{{ $mondayKey + 1 }}">
                            <td class="pt-0" style="width: 45%">
                                <input type="time" name="monday_from[]" class="form-control"
                                    value="{{ explode('-', $monday)[0] }}" />
                            </td>
                            <td class="pt-0" style="width: 45%">
                                <input type="time" name="monday_to[]" class="form-control"
                                    value="{{ explode('-', $monday)[1] }}" />
                            </td>
                            @if ($loop->first)
                                <td class="pt-0" style="width: 10%;">
                                    <button type="button" id="monday-update-add"
                                        class="btn btn-primary"><i>+</i></button>
                                </td>
                            @else
                                <td class="pt-0" style="width: 10%;">
                                    <button type="button" id="{{ $mondayKey + 1 }}"
                                        class="btn btn-danger monday-update-btn-remove"><i>x</i></button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @endif
            </table>

            <h4>Tuesday</h4>
            <table class="table table-borderless table-hover" id="tuesday-update-dynamic-field">
                <tr>
                    <td class="pb-0">
                        <label for="" class="form-label">From Time</label>
                    </td>
                    <td class="pb-0">
                        <label for="" class="form-label">To Time</label>
                    </td>
                </tr>
                @if (empty(json_decode($availability['tuesday'])))
                    <tr>
                        <td class="pt-0" style="width: 45%">
                            <input type="time" name="tuesday_from[]" class="form-control" />
                        </td>
                        <td class="pt-0" style="width: 45%">
                            <input type="time" name="tuesday_to[]" class="form-control" />
                        </td>
                        <td class="pt-0" style="width: 10%;">
                            <button type="button" id="tuesday-update-add" class="btn btn-primary"><i>+</i></button>
                        </td>
                    </tr>
                @else
                    @foreach (json_decode($availability['tuesday']) as $tuesdayKey => $tuesday)
                        <tr id="tuesday-update-row{{ $tuesdayKey + 1 }}">
                            <td class="pt-0" style="width: 45%">
                                <input type="time" name="tuesday_from[]" class="form-control"
                                    value="{{ explode('-', $tuesday)[0] }}" />
                            </td>
                            <td class="pt-0" style="width: 45%">
                                <input type="time" name="tuesday_to[]" class="form-control"
                                    value="{{ explode('-', $tuesday)[1] }}" />
                            </td>
                            @if ($loop->first)
                                <td class="pt-0" style="width: 10%;">
                                    <button type="button" id="tuesday-update-add"
                                        class="btn btn-primary"><i>+</i></button>
                                </td>
                            @else
                                <td class="pt-0" style="width: 10%;">
                                    <button type="button" id="{{ $tuesdayKey + 1 }}"
                                        class="btn btn-danger tuesday-update-btn-remove"><i>x</i></button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @endif
            </table>

            <h4>Wednesday</h4>
            <table class="table table-borderless table-hover" id="wednesday-update-dynamic-field">
                <tr>
                    <td class="pb-0">
                        <label for="" class="form-label">From Time</label>
                    </td>
                    <td class="pb-0">
                        <label for="" class="form-label">To Time</label>
                    </td>
                </tr>
                @if (empty(json_decode($availability['wednesday'])))
                    <tr>
                        <td class="pt-0" style="width: 45%">
                            <input type="time" name="wednesday_from[]" class="form-control" />
                        </td>
                        <td class="pt-0" style="width: 45%">
                            <input type="time" name="wednesday_to[]" class="form-control" />
                        </td>
                        <td class="pt-0" style="width: 10%;">
                            <button type="button" id="wednesday-update-add"
                                class="btn btn-primary"><i>+</i></button>
                        </td>
                    </tr>
                @else
                    @foreach (json_decode($availability['wednesday']) as $wednesdayKey => $wednesday)
                        <tr id="wednesday-update-row{{ $wednesdayKey + 1 }}">
                            <td class="pt-0" style="width: 45%">
                                <input type="time" name="wednesday_from[]" class="form-control"
                                    value="{{ explode('-', $wednesday)[0] }}" />
                            </td>
                            <td class="pt-0" style="width: 45%">
                                <input type="time" name="wednesday_to[]" class="form-control"
                                    value="{{ explode('-', $wednesday)[1] }}" />
                            </td>
                            @if ($loop->first)
                                <td class="pt-0" style="width: 10%;">
                                    <button type="button" id="wednesday-update-add"
                                        class="btn btn-primary"><i>+</i></button>
                                </td>
                            @else
                                <td class="pt-0" style="width: 10%;">
                                    <button type="button" id="{{ $wednesdayKey + 1 }}"
                                        class="btn btn-danger wednesday-update-btn-remove"><i>x</i></button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @endif
            </table>

            <h4>Thursday</h4>
            <table class="table table-borderless table-hover" id="thursday-update-dynamic-field">
                <tr>
                    <td class="pb-0">
                        <label for="" class="form-label">From Time</label>
                    </td>
                    <td class="pb-0">
                        <label for="" class="form-label">To Time</label>
                    </td>
                </tr>
                @if (empty(json_decode($availability['thursday'])))
                    <tr>
                        <td class="pt-0" style="width: 45%">
                            <input type="time" name="thursday_from[]" class="form-control" />
                        </td>
                        <td class="pt-0" style="width: 45%">
                            <input type="time" name="thursday_to[]" class="form-control" />
                        </td>
                        <td class="pt-0" style="width: 10%;">
                            <button type="button" id="thursday-update-add" class="btn btn-primary"><i>+</i></button>
                        </td>
                    </tr>
                @else
                    @foreach (json_decode($availability['thursday']) as $thursdayKey => $thursday)
                        <tr id="thursday-update-row{{ $thursdayKey + 1 }}">
                            <td class="pt-0" style="width: 45%">
                                <input type="time" name="thursday_from[]" class="form-control"
                                    value="{{ explode('-', $thursday)[0] }}" />
                            </td>
                            <td class="pt-0" style="width: 45%">
                                <input type="time" name="thursday_to[]" class="form-control"
                                    value="{{ explode('-', $thursday)[1] }}" />
                            </td>
                            @if ($loop->first)
                                <td class="pt-0" style="width: 10%;">
                                    <button type="button" id="thursday-update-add"
                                        class="btn btn-primary"><i>+</i></button>
                                </td>
                            @else
                                <td class="pt-0" style="width: 10%;">
                                    <button type="button" id="{{ $thursdayKey + 1 }}"
                                        class="btn btn-danger thursday-update-btn-remove"><i>x</i></button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @endif
            </table>

            <h4>Friday</h4>
            <table class="table table-borderless table-hover" id="friday-update-dynamic-field">
                <tr>
                    <td class="pb-0">
                        <label for="" class="form-label">From Time</label>
                    </td>
                    <td class="pb-0">
                        <label for="" class="form-label">To Time</label>
                    </td>
                </tr>
                @if (empty(json_decode($availability['friday'])))
                    <tr>
                        <td class="pt-0" style="width: 45%">
                            <input type="time" name="friday_from[]" class="form-control" />
                        </td>
                        <td class="pt-0" style="width: 45%">
                            <input type="time" name="friday_to[]" class="form-control" />
                        </td>
                        <td class="pt-0" style="width: 10%;">
                            <button type="button" id="friday-update-add" class="btn btn-primary"><i>+</i></button>
                        </td>
                    </tr>
                @else
                    @foreach (json_decode($availability['friday']) as $fridayKey => $friday)
                        <tr id="friday-update-row{{ $fridayKey + 1 }}">
                            <td class="pt-0" style="width: 45%">
                                <input type="time" name="friday_from[]" class="form-control"
                                    value="{{ explode('-', $friday)[0] }}" />
                            </td>
                            <td class="pt-0" style="width: 45%">
                                <input type="time" name="friday_to[]" class="form-control"
                                    value="{{ explode('-', $friday)[1] }}" />
                            </td>
                            @if ($loop->first)
                                <td class="pt-0" style="width: 10%;">
                                    <button type="button" id="friday-update-add"
                                        class="btn btn-primary"><i>+</i></button>
                                </td>
                            @else
                                <td class="pt-0" style="width: 10%;">
                                    <button type="button" id="{{ $fridayKey + 1 }}"
                                        class="btn btn-danger friday-update-btn-remove"><i>x</i></button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @endif
            </table>

            <h4>Saturday</h4>
            <table class="table table-borderless table-hover" id="saturday-update-dynamic-field">
                <tr>
                    <td class="pb-0">
                        <label for="" class="form-label">From Time</label>
                    </td>
                    <td class="pb-0">
                        <label for="" class="form-label">To Time</label>
                    </td>
                </tr>
                @if (empty(json_decode($availability['saturday'])))
                    <tr>
                        <td class="pt-0" style="width: 45%">
                            <input type="time" name="saturday_from[]" class="form-control" />
                        </td>
                        <td class="pt-0" style="width: 45%">
                            <input type="time" name="saturday_to[]" class="form-control" />
                        </td>
                        <td class="pt-0" style="width: 10%;">
                            <button type="button" id="saturday-update-add" class="btn btn-primary"><i>+</i></button>
                        </td>
                    </tr>
                @else
                    @foreach (json_decode($availability['saturday']) as $saturdayKey => $saturday)
                        <tr id="saturday-update-row{{ $saturdayKey + 1 }}">
                            <td class="pt-0" style="width: 45%">
                                <input type="time" name="saturday_from[]" class="form-control"
                                    value="{{ explode('-', $saturday)[0] }}" />
                            </td>
                            <td class="pt-0" style="width: 45%">
                                <input type="time" name="saturday_to[]" class="form-control"
                                    value="{{ explode('-', $saturday)[1] }}" />
                            </td>
                            @if ($loop->first)
                                <td class="pt-0" style="width: 10%;">
                                    <button type="button" id="saturday-update-add"
                                        class="btn btn-primary"><i>+</i></button>
                                </td>
                            @else
                                <td class="pt-0" style="width: 10%;">
                                    <button type="button" id="{{ $saturdayKey + 1 }}"
                                        class="btn btn-danger saturday-update-btn-remove"><i>x</i></button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @endif
            </table>
        </div>
    </div>
</div>
