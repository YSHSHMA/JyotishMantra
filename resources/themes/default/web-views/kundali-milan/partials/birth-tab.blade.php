<div class="tab-pane fade show active" id="birth-info" role="tabpanel" aria-labelledby="birth-info-tab">
    <div class="row">
        <div class="col-md-12">
            <div class="tab-details-block">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">जन्म विवरण</h6>
                        <div class="kundli-basic-details1 tableFixHead">
                            <table class="table">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col"><b> विवरण</b></th>
                                        <th scope="col"><b>पुरुष </b></th>
                                        <th scope="col"><b>महिला </b></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    <tr>
                                        <td>
                                            <b class="mb-0">जन्म दिनांक</b>
                                        </td>
                                        <td>{{ isset($savedMaleDOB)?date('d/m/Y',strtotime($savedMaleDOB)):$usersData['male_dob'] }}</td>
                                        <td>{{ isset($savedFemaleDOB)?date('d/m/Y',strtotime($savedFemaleDOB)):$usersData['female_dob'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b class="mb-0">जन्म समय</b>
                                        </td>
                                        <td>{{ $usersData['male_time'] }}</td>
                                        <td>{{ $usersData['female_time'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b class="mb-0">अक्षांश</b>
                                        </td>
                                        <td>{{ $usersData['male_latitude'] }}</td>
                                        <td>{{ $usersData['female_latitude'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b class="mb-0">रेखांश</b>
                                        </td>
                                        <td>{{ $usersData['male_longitude'] }}</td>
                                        <td>{{ $usersData['female_longitude'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b class="mb-0">टाइम जोन</b>
                                        </td>
                                        <td>{{ $usersData['male_timezone'] }}</td>
                                        <td>{{ $usersData['female_timezone'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b class="mb-0">सूर्योदय</b>
                                        </td>
                                        <td>{{ $birthData['male_astro_details']['sunrise'] }}</td>
                                        <td>{{ $birthData['female_astro_details']['sunrise'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b class="mb-0">सूर्यास्त</b>
                                        </td>
                                        <td>{{ $birthData['male_astro_details']['sunset'] }}</td>
                                        <td>{{ $birthData['female_astro_details']['sunset'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b class="mb-0">अयनांश</b>
                                        </td>
                                        <td>{{ $birthData['male_astro_details']['ayanamsha'] }}</td>
                                        <td>{{ $birthData['female_astro_details']['ayanamsha'] }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">अवकहडा विवरण</h6>
                        <div class="kundli-basic-details1 tableFixHead">
                            <table class="table">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col"><b> विवरण</b></th>
                                        <th scope="col"><b>पुरुष </b></th>
                                        <th scope="col"><b>महिला </b></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    <tr>
                                        <td>
                                            <b class="mb-0">वर्ण</b>
                                        </td>
                                        <td>
                                            <p id="m_varn" class="mb-0">
                                                {{ $astroData['male_astro_details']['Varna'] }}</p>
                                        </td>
                                        <td>
                                            <p id="f_varn" class="mb-0">
                                                {{ $astroData['female_astro_details']['Varna'] }}</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b class="mb-0">वश्य</b>
                                        </td>
                                        <td>
                                            <p id="m_vashya" class="mb-0">
                                                {{ $astroData['male_astro_details']['Vashya'] }}</p>
                                        </td>
                                        <td>
                                            <p id="f_vashya" class="mb-0">
                                                {{ $astroData['female_astro_details']['Vashya'] }}</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b class="mb-0">योनी</b>
                                        </td>
                                        <td>
                                            <p id="m_yoni" class="mb-0">
                                                {{ $astroData['male_astro_details']['Yoni'] }}</p>
                                        </td>
                                        <td>
                                            <p id="f_yoni" class="mb-0">
                                                {{ $astroData['female_astro_details']['Yoni'] }}</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b class="mb-0">गण</b>
                                        </td>
                                        <td>
                                            <p id="m_gan" class="mb-0">
                                                {{ $astroData['male_astro_details']['Gan'] }}</p>
                                        </td>
                                        <td>
                                            <p id="f_gan" class="mb-0">
                                                {{ $astroData['female_astro_details']['Gan'] }}</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b class="mb-0">नाडी</b>
                                        </td>
                                        <td>
                                            <p id="m_nadi" class="mb-0">
                                                {{ $astroData['male_astro_details']['Nadi'] }}</p>
                                        </td>
                                        <td>
                                            <p id="f_nadi" class="mb-0">
                                                {{ $astroData['female_astro_details']['Nadi'] }}</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b class="mb-0">राशि स्वामी</b>
                                        </td>
                                        <td>
                                            <p id="m_rashi_swami" class="mb-0">
                                                {{ $astroData['male_astro_details']['SignLord'] }} </p>
                                        </td>
                                        <td>
                                            <p id="f_rashi_swami" class="mb-0">
                                                {{ $astroData['female_astro_details']['SignLord'] }} </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b class="mb-0">नक्षत्र</b>
                                        </td>
                                        <td>
                                            <span id="m_nakshatraa">{{ $astroData['male_astro_details']['Naksahtra'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                id="f_nakshatraa">{{ $astroData['female_astro_details']['Naksahtra'] }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b class="mb-0">नक्षत्र देवता</b>
                                        </td>
                                        <td>
                                            <span
                                                id="m_nakshatraa_dev">{{ $astroData['male_astro_details']['NaksahtraLord'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                id="f_nakshatraa_dev">{{ $astroData['female_astro_details']['NaksahtraLord'] }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b class="mb-0">चरण</b>
                                        </td>
                                        <td>
                                            <span
                                                id="m_charan">{{ $astroData['male_astro_details']['Charan'] }}</span>
                                        </td>
                                        <td>
                                            <span
                                                id="f_charan">{{ $astroData['female_astro_details']['Charan'] }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b class="mb-0">योग</b>
                                        </td>
                                        <td>
                                            <p id="m_yogg" class="mb-0">
                                                {{ $astroData['male_astro_details']['Yog'] }}</p>
                                        </td>
                                        <td>
                                            <p id="f_yogg" class="mb-0">
                                                {{ $astroData['female_astro_details']['Yog'] }}</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b class="mb-0">करन</b>
                                        </td>
                                        <td>
                                            <p id="m_karann" class="mb-0">
                                                {{ $astroData['male_astro_details']['Karan'] }}</p>
                                        </td>
                                        <td>
                                            <p id="f_karann" class="mb-0">
                                                {{ $astroData['female_astro_details']['Karan'] }}</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b class="mb-0">तिथि</b>
                                        </td>
                                        <td>
                                            <p id="m_tithii" class="mb-0">
                                                {{ $astroData['male_astro_details']['Tithi'] }}</p>
                                        </td>
                                        <td>
                                            <p id="f_tithii" class="mb-0">
                                                {{ $astroData['female_astro_details']['Tithi'] }}</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b class="mb-0">युन्जा</b>
                                        </td>
                                        <td>
                                            <p id="m_yunja" class="mb-0">
                                                {{ $astroData['male_astro_details']['yunja'] }}</p>
                                        </td>
                                        <td>
                                            <p id="f_yunja" class="mb-0">
                                                {{ $astroData['female_astro_details']['yunja'] }}</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b class="mb-0">तत्व</b>
                                        </td>
                                        <td>
                                            <p id="m_tatva" class="mb-0">
                                                {{ $astroData['male_astro_details']['tatva'] }} </p>
                                        </td>
                                        <td>
                                            <p id="f_tatva" class="mb-0">
                                                {{ $astroData['female_astro_details']['tatva'] }} </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b class="mb-0">नाम अक्षर</b>
                                        </td>
                                        <td>
                                            <p id="m_naam_varnmala" class="mb-0">
                                                {{ $astroData['male_astro_details']['name_alphabet'] }}</p>
                                        </td>
                                        <td>
                                            <p id="f_naam_varnmala" class="mb-0">
                                                {{ $astroData['female_astro_details']['name_alphabet'] }}</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b class="mb-0">पाया</b>
                                        </td>
                                        <td>
                                            <p id="m_paya" class="mb-0">
                                                {{ $astroData['male_astro_details']['paya'] }} </p>
                                        </td>
                                        <td>
                                            <p id="f_paya" class="mb-0">
                                                {{ $astroData['female_astro_details']['paya'] }} </p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
