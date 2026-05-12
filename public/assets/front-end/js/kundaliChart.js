function getApiData(apiName, requestData) {
    var userId = "6030",
        apiKey = "e9c5f9c214dc6ef8f7b3e44ee550ac25";

    var request = $.ajax({
        url: "https://json.astrologyapi.com/v1/" + apiName,
        method: "POST",
        dataType: 'json',
        headers: {
            authorization: "Basic " + btoa(userId + ":" + apiKey),
            "Content-Type": "application/json",
            "Accept-Language": "hi" // bn,ma,kn,ml,te,ta,en
        },
        data: JSON.stringify(requestData)
    });
    return request.done(function (msg) {
        return msg;
    });
}

var planet_abbr = {
    SUN: 'Su',
    MOON: 'Mo',
    MARS: 'Ma',
    MERCURY: 'Me',
    JUPITER: 'Ju',
    VENUS: 'Ve',
    SATURN: 'Sa',
    RAHU: 'Ra',
    KETU: 'Ke',
    URANUS: 'Ur',
    NEPTUNE: 'Ne',
    PLUTO: 'pl',
    "सूर्य": "सूर्य", "चन्द्र": "चन्द्र", "मंगल": "मंगल", "बुध": "बुध", "बुध ": "बुध", "गुरु": "गुरु", "शुक्र": "शुक्र", "शनि": "शनि", "राहु": "राहु", "केतु": "केतु", "प्लूटो;": "प्लूटो", "यूरेनस": "यूरेनस", "नेप्चून": "नेप्चून"
};

function getSignPlanetArray(data) {
    var resp = ['', '', '', '', '', '', '', '', '', '', '', ''];
    for (var i = 0; i < data.length; i++) {
        for (var j = 0; j < data[i]['planet'].length; j++) {
            resp[data[i]['sign'] - 1] += (planet_abbr[data[i]['planet'][j]]) + ' ';
        }
    }
    return resp;
}

function getPlanetArray(data) {
    var resp = ['', '', '', '', '', '', '', '', '', '', '', ''];
    for (var i = 0; i < data.length; i++) {
        for (var j = 0; j < data[i]['planet'].length; j++) {
            resp[i] += (planet_abbr[data[i]['planet'][j]]) + ' ';
        }
    }
    return resp;

}

function getSignArray(data) {
    var resp = [];
    for (var i = 0; i < data.length; i++) {
        resp.push(data[i].sign);
    }
    return resp;
}


// start janam kundali char
var userData = "";
function sendData(data) {
    userData = data;
    console.log(userData);
}

function getNorthHoroCharts(options, chartId) {
    getApiData('horo_chart/' + chartId, userData).then(function (res) {
        drawNorthChart(getPlanetArray(res), getSignArray(res), options, '#northChart');
    },
        function (error) {
            alert('Some error occured, plz try again');
        });
}

function getSouthHoroCharts(options, chartId) {
    getApiData('horo_chart/' + chartId, userData).then(function (res) {
        drawSouthChart(getSignPlanetArray(res), res[0].sign, options, '#southChart');
    },
        function (error) {
            alert('Some error occured, plz try again');
        });
}
// end janam kundali char

// start matching chart function

var matchMaleData = "";
function sendMaleData(mdata) {
    matchMaleData = mdata;
}

var matchFemaleData = "";
function sendFemaleData(fdata) {
    matchFemaleData = fdata;
}

function getMaleCharts(options, chartId, chartPlace, isForNorth) {

    getApiData('horo_chart/' + chartId, matchMaleData).then(function (res) {
        if (isForNorth) {
            drawNorthChart(getPlanetArray(res), getSignArray(res), options, '#' + chartPlace);
        }
        else {
            drawSouthChart(getSignPlanetArray(res), res[0].sign, options, '#' + chartPlace);
        }
    },
        function (error) {
            alert('Some error occured, plz try again');
        });
}

function getFemaleCharts(options, chartId, chartPlace, isForNorth) {
    getApiData('horo_chart/' + chartId, matchFemaleData).then(function (res) {
        if (isForNorth) {
            drawNorthChart(getPlanetArray(res), getSignArray(res), options, '#' + chartPlace);
        }
        else {
            drawSouthChart(getSignPlanetArray(res), res[0].sign, options, '#' + chartPlace);
        }
    },
        function (error) {
            alert('Some error occured, plz try again');
        });
}
// end matching chart function


//Draw North Chart
function drawNorthChart(t, e, r, a) { function n(t) { u = t, h = 0, y = 0, d = u - 2 * h, f = d - 2 * h, g = d / 2, c = f / 2, C = c / 2, k = g / 2, S[0] = h, m[0] = y, S[1] = h + d, m[1] = y, S[2] = h + d, m[2] = y + f, S[3] = h, m[3] = y + f, S[4] = h + g, m[4] = y, S[5] = h + d, m[5] = y + c, S[6] = h + g, m[6] = y + f, S[7] = h, m[7] = y + c, b = (S[1] - S[0] + (m[1] - m[0])) / 10, z = (S[3] - S[0] + (m[3] - m[0])) / 10, v = (S[1] - S[0] + (m[1] - m[0])) / 20, w = (S[3] - S[0] + (m[3] - m[0])) / 20 } function s(t, e) { var r = d / 28 + "px", a = t.append("text").text("11").attr("font-size", r), n = a.node().getBoundingClientRect(), s = n.width / 2, l = n.height / 2; t.append("text").text("" + e[0]).attr("font-size", r).attr("x", S[4] - s).attr("y", m[7] - z).style("fill", N.signColor), t.append("text").text("" + e[1]).attr("font-size", r).attr("x", S[4] - k - s).attr("y", m[7] - C - z).style("fill", N.signColor), t.append("text").text("" + e[2]).attr("font-size", r).attr("x", S[4] - k - b).attr("y", m[7] - C - l).style("fill", N.signColor), t.append("text").text("" + e[3]).attr("font-size", r).attr("x", S[4] - k - s).attr("y", m[6] - C - z).style("fill", N.signColor), t.append("text").text("" + e[4]).attr("font-size", r).attr("x", S[4] - k - b).attr("y", m[6] - C - l).style("fill", N.signColor), t.append("text").text("" + e[5]).attr("font-size", r).attr("x", S[7] + k - s).attr("y", m[6] - C + z).style("fill", N.signColor), t.append("text").text("" + e[6]).attr("font-size", r).attr("x", S[4] - s).attr("y", m[7] + z).style("fill", N.signColor), t.append("text").text("" + e[7]).attr("font-size", r).attr("x", S[4] + k - s).attr("y", m[6] - C + z).style("fill", N.signColor), t.append("text").text("" + e[8]).attr("font-size", r).attr("x", S[4] + k + 1.5 * v).attr("y", m[7] + C - l).style("fill", N.signColor), t.append("text").text("" + e[9]).attr("font-size", r).attr("x", S[4] + k - s).attr("y", m[6] - C - z).style("fill", N.signColor), t.append("text").text("" + e[10]).attr("font-size", r).attr("x", S[4] + k + 1.5 * v).attr("y", m[4] + C - l).style("fill", N.signColor), t.append("text").text("" + e[11]).attr("font-size", r).attr("x", S[4] + k - s).attr("y", m[4] + C - z).style("fill", N.signColor) } function l(t, e, r, a, n) { var s, l, o, p = d / 30 + "px", i = t.append("text").text(e).attr("font-size", p).node().getBoundingClientRect(), x = t.append("text").text(r).attr("font-size", p).node().getBoundingClientRect(); s = i.height, l = i.width / 2, o = x.width / 2; var h = l, y = 2 * z, f = o, u = 2 * z + s + v; r.length > 1 && (t.append("text").text(r).attr("font-size", p).attr("x", a - f).attr("y", n + u).style("fill", N.planetColor), e.length > 1 && t.append("text").text(e).attr("font-size", p).attr("x", a - h).attr("y", n + y).style("fill", N.planetColor)) } function o(t, e, r, a, n, s) { var l, o, p, i = d / 30 + "px", x = t.append("text").text(e).attr("font-size", i).node().getBoundingClientRect(), h = t.append("text").text(r).attr("font-size", i).node().getBoundingClientRect(); l = x.height, o = x.width / 2, p = h.width / 2; var y = o, f = w, u = p, g = v + l; s ? e.length > 1 && (t.append("text").text(e).attr("font-size", i).attr("x", a + k - y).attr("y", n + f).style("fill", N.planetColor), r.length > 1 && t.append("text").text(r).attr("font-size", i).attr("x", a + k - u).attr("y", n + g).style("fill", N.planetColor)) : r.length > 1 && (t.append("text").text(r).attr("font-size", i).attr("x", a + k - u).attr("y", n - f).style("fill", N.planetColor), e.length > 1 && t.append("text").text(e).attr("font-size", i).attr("x", a + k - y).attr("y", n - g).style("fill", N.planetColor)) } function p(t, e, r, a, n, s, l, o) { var p = d / 30 + "px", i = t.append("text").text(e).attr("font-size", p).node().getBoundingClientRect(), x = i.height, h = 3 * w, y = w / 2, f = b / 5 + x; o ? e.length > 1 && (t.append("text").text(e).attr("font-size", p).attr("x", s + y).attr("y", l + h + f).style("fill", N.planetColor), t.append("text").text(r).attr("font-size", p).attr("x", s + y).attr("y", l + h + 2 * f).style("fill", N.planetColor), t.append("text").text(a).attr("font-size", p).attr("x", s + y).attr("y", l + h + 3 * f).style("fill", N.planetColor), t.append("text").text(n).attr("font-size", p).attr("x", s + y).attr("y", l + h).style("fill", N.planetColor)) : e.length > 1 && (t.append("text").text(e).attr("font-size", p).attr("x", s - y).attr("y", l + h + f).attr("text-anchor", "end").style("fill", N.planetColor), t.append("text").text(r).attr("font-size", p).attr("x", s - y).attr("y", l + h + 2 * f).attr("text-anchor", "end").style("fill", N.planetColor), t.append("text").text(a).attr("font-size", p).attr("x", s - y).attr("y", l + h + 3 * f).attr("text-anchor", "end").style("fill", N.planetColor), t.append("text").text(n).attr("font-size", p).attr("x", s - y).attr("y", l + h).attr("text-anchor", "end").style("fill", N.planetColor)) } function i() { for (var t = 0; t < B.length; t++) { var e = B[t]; switch (null != e && e.length > 0 || (e = " "), t) { case 0: if (e.length > 14) { var r = e.substring(0, 14), a = e.substring(14, e.length); j[0] = r, j[1] = a } else j[0] = e, j[1] = ""; break; case 1: if (e.length > 14) { var r = e.substring(0, 14), a = e.substring(14, e.length); q[0] = r, q[1] = a } else q[0] = e, q[1] = ""; break; case 2: x(e, A); break; case 3: if (e.length > 14) { var r = e.substring(0, 14), a = e.substring(14, e.length); D[0] = r, D[1] = a } else D[0] = e, D[1] = ""; break; case 4: x(e, E); break; case 5: if (e.length > 14) { var r = e.substring(0, 14), a = e.substring(14, e.length); F[0] = r, F[1] = a } else F[0] = e, F[1] = ""; break; case 6: if (e.length > 14) { var r = e.substring(0, 14), a = e.substring(14, e.length); G[0] = r, G[1] = a } else G[0] = e, G[1] = ""; break; case 7: if (e.length > 14) { var r = e.substring(0, 14), a = e.substring(14, e.length); H[0] = r, H[1] = a } else H[0] = e, H[1] = ""; break; case 8: x(e, I); break; case 9: if (e.length > 14) { var r = e.substring(0, 14), a = e.substring(14, e.length); J[0] = r, J[1] = a } else J[0] = e, J[1] = ""; break; case 10: x(e, K); break; case 11: if (e.length > 14) { var r = e.substring(0, 14), a = e.substring(14, e.length); L[0] = r, L[1] = a } else L[0] = e, L[1] = "" } } V(Q, R) } function x(t, e) { if (t.length > 8) { e[0] = t.substring(0, 8); try { e[1] = t.substring(9, 17) } catch (r) { return e[1] = "", e[2] = "", void (e[3] = "") } try { e[2] = t.substring(18, 20) } catch (r) { e[2] = "", e[3] = "" } try { e[3] = t.substring(21, t.length) } catch (r) { e[3] = "" } } else e[0] = t, e[1] = "", e[2] = "", e[3] = "" } var h, y, d, f, u, g, c, C, k, b, z, v, w, B, S = [], m = [], N = { lineColor: "black", planetColor: "black", signColor: "black", width: 350 }, j = ["", ""], q = ["", ""], A = ["", "", "", ""], D = ["", ""], E = ["", "", "", ""], F = ["", ""], G = ["", ""], H = ["", ""], I = ["", "", "", ""], J = ["", ""], K = ["", "", "", ""], L = ["", ""], M = ["", "", "", "", "", "", "", "", "", "", "", ""]; B = [[""], [""], [""], [""], [""], [""], [""], [""], [""], [""], [""], [""]], t && (B = t), e && (M = e), r && (r.width && (N.width = r.width), r.lineColor && (N.lineColor = r.lineColor), r.planetColor && (N.planetColor = r.planetColor), r.signColor && (N.signColor = r.signColor)), n(N.width); var O, P, Q = d, R = f, T = d3.svg.line().x(function (t) { return t.x }).y(function (t) { return t.y }).interpolate("linear"), U = function (t) { var e = []; switch (t) { case 1: e.push({ x: 0, y: 0 }), e.push({ x: Q / 2, y: 0 }), e.push({ x: Q / 4, y: R / 4 }), e.push({ x: 0, y: 0 }); break; case 2: e.push({ x: Q / 2, y: 0 }), e.push({ x: Q, y: 0 }), e.push({ x: Q - Q / 4, y: R / 4 }), e.push({ x: Q / 2, y: 0 }); break; case 3: e.push({ x: 0, y: 0 }), e.push({ x: Q / 4, y: R / 4 }), e.push({ x: 0, y: R / 2 }), e.push({ x: 0, y: 0 }); break; case 4: e.push({ x: Q / 2, y: 0 }), e.push({ x: Q / 4, y: R / 4 }), e.push({ x: Q / 2, y: R / 2 }), e.push({ x: Q - Q / 4, y: R / 4 }), e.push({ x: Q / 2, y: 0 }); break; case 5: e.push({ x: Q, y: 0 }), e.push({ x: Q - Q / 4, y: R / 4 }), e.push({ x: Q, y: R / 2 }), e.push({ x: Q, y: 0 }); break; case 6: e.push({ x: 0, y: R / 2 }), e.push({ x: Q / 4, y: R / 4 }), e.push({ x: Q / 2, y: R / 2 }), e.push({ x: Q / 4, y: R - R / 4 }), e.push({ x: 0, y: R / 2 }); break; case 7: e.push({ x: Q / 2, y: R / 2 }), e.push({ x: Q - Q / 4, y: R / 4 }), e.push({ x: Q, y: R / 2 }), e.push({ x: Q - Q / 4, y: R - R / 4 }), e.push({ x: Q / 2, y: R / 2 }); break; case 8: e.push({ x: 0, y: R / 2 }), e.push({ x: Q / 4, y: R - R / 4 }), e.push({ x: 0, y: R }), e.push({ x: 0, y: R / 2 }); break; case 9: e.push({ x: Q / 4, y: R - R / 4 }), e.push({ x: Q / 2, y: R / 2 }), e.push({ x: Q - Q / 4, y: R - R / 4 }), e.push({ x: Q / 2, y: R }), e.push({ x: Q / 4, y: R - R / 4 }); break; case 10: e.push({ x: Q - Q / 4, y: R - R / 4 }), e.push({ x: Q, y: R / 2 }), e.push({ x: Q, y: R }), e.push({ x: Q - Q / 4, y: R - R / 4 }); break; case 11: e.push({ x: 0, y: R }), e.push({ x: Q / 4, y: R - R / 4 }), e.push({ x: Q / 2, y: R }), e.push({ x: 0, y: R }); break; case 12: e.push({ x: Q / 2, y: R }), e.push({ x: Q - Q / 4, y: R - R / 4 }), e.push({ x: Q, y: R }), e.push({ x: Q / 2, y: R }) }return e }, V = function (t, e) { d3.select(a + " svg").remove(), O = d3.select(a).append("svg:svg").attr("width", t).attr("height", e).attr("id", "chartSvg").append("g"), Q = t, R = e, P = O.append("path").attr("d", T(U(1))).attr("stroke", N.lineColor).attr("stroke-width", 2).attr("fill", "none"), P = O.append("path").attr("d", T(U(2))).attr("stroke", N.lineColor).attr("stroke-width", 2).attr("fill", "none"), P = O.append("path").attr("d", T(U(3))).attr("stroke", N.lineColor).attr("stroke-width", 2).attr("fill", "none"), P = O.append("path").attr("d", T(U(4))).attr("stroke", N.lineColor).attr("stroke-width", 2).attr("fill", "none"), P = O.append("path").attr("d", T(U(5))).attr("stroke", N.lineColor).attr("stroke-width", 2).attr("fill", "none"), P = O.append("path").attr("d", T(U(6))).attr("stroke", N.lineColor).attr("stroke-width", 2).attr("fill", "none"), P = O.append("path").attr("d", T(U(7))).attr("stroke", N.lineColor).attr("stroke-width", 2).attr("fill", "none"), P = O.append("path").attr("d", T(U(8))).attr("stroke", N.lineColor).attr("stroke-width", 2).attr("fill", "none"), P = O.append("path").attr("d", T(U(9))).attr("stroke", N.lineColor).attr("stroke-width", 2).attr("fill", "none"), P = O.append("path").attr("d", T(U(10))).attr("stroke", N.lineColor).attr("stroke-width", 2).attr("fill", "none"), P = O.append("path").attr("d", T(U(11))).attr("stroke", N.lineColor).attr("stroke-width", 2).attr("fill", "none"), P = O.append("path").attr("d", T(U(12))).attr("stroke", N.lineColor).attr("stroke-width", 2).attr("fill", "none"); O.append("text").text("Sun").attr("font-size", "20px"); s(O, M), l(O, j[1], j[0], S[4], m[4]), l(O, D[1], D[0], S[0] + k, m[0] + C), l(O, G[1], G[0], S[4], m[4] + c), l(O, J[1], J[0], S[4] + k, m[4] + C), o(O, q[0], q[1], S[0], m[0], !0), o(O, L[0], L[1], S[4], m[4], !0), o(O, F[1], F[0], S[3], m[3], !1), o(O, H[1], H[0], S[6], m[6], !1), p(O, A[0], A[1], A[2], A[3], S[0], m[0], !0), p(O, E[0], E[1], E[2], E[3], S[7], m[7], !0), p(O, K[0], K[1], K[2], K[3], S[1], m[1], !1), p(O, I[0], I[1], I[2], I[3], S[5], m[5], !1) }; i() }

//Draw South Chart
function drawSouthChart(t, e, a, r) { function n(t) { g = t, p = 0, x = 0, h = g - 2 * p, u = h - 2 * p, d = h / 2, y = u / 2, f = y / 2, b = d / 2, v[0] = p, w[0] = x, v[1] = p + h, w[1] = x, v[2] = p + h, w[2] = x + u, v[3] = p, w[3] = x + u, v[4] = p + d, w[4] = x, v[5] = p + h, w[5] = x + y, v[6] = p + d, w[6] = x + u, v[7] = p, w[7] = x + y, c = (v[1] - v[0] + (w[1] - w[0])) / 10, k = (v[3] - v[0] + (w[3] - w[0])) / 10, C = (v[1] - v[0] + (w[1] - w[0])) / 20, M = (v[3] - v[0] + (w[3] - w[0])) / 20 } function s(t, e) { d3.select(r + " svg").remove(), F = d3.select(r).append("svg:svg").attr("width", t).attr("height", e).attr("id", "chartSvg").append("g"); F.append("rect").attr("x", p).attr("y", x).attr("width", t).attr("height", e).attr("stroke", z.lineColor).attr("stroke-width", 3).attr("fill", "none"); F.append("line").attr("x1", p + b).attr("y1", x).attr("x2", p + b).attr("y2", x + u).attr("stroke-width", 2).attr("stroke", z.lineColor), F.append("line").attr("x1", p + 3 * b).attr("y1", x).attr("x2", p + 3 * b).attr("y2", x + u).attr("stroke-width", 2).attr("stroke", z.lineColor), F.append("line").attr("x1", p).attr("y1", x + f).attr("x2", p + h).attr("y2", x + f).attr("stroke-width", 2).attr("stroke", z.lineColor), F.append("line").attr("x1", p).attr("y1", x + 3 * f).attr("x2", p + h).attr("y2", x + 3 * f).attr("stroke-width", 2).attr("stroke", z.lineColor), F.append("line").attr("x1", p).attr("y1", x + 2 * f).attr("x2", p + b).attr("y2", x + 2 * f).attr("stroke-width", 2).attr("stroke", z.lineColor), F.append("line").attr("x1", p + 3 * b).attr("y1", x + 2 * f).attr("x2", p + h).attr("y2", x + 2 * f).attr("stroke-width", 2).attr("stroke", z.lineColor), F.append("line").attr("x1", p + 2 * b).attr("y1", x).attr("x2", p + 2 * b).attr("y2", x + f).attr("stroke-width", 2).attr("stroke", z.lineColor), F.append("line").attr("x1", p + 2 * b).attr("y1", x + 3 * f).attr("x2", p + 2 * b).attr("y2", x + u).attr("stroke-width", 2).attr("stroke", z.lineColor), l(F, J[0], J[1], J[2], p + b, x, o(1)), l(F, K[0], K[1], K[2], p + 2 * b, x, o(2)), l(F, R[0], R[1], R[2], p + 3 * b, x, o(3)), l(F, B[0], B[1], B[2], p + 3 * f, x + b, o(4)), l(F, N[0], N[1], N[2], p + 3 * f, x + 2 * b, o(5)), l(F, P[0], P[1], P[2], p + 3 * f, x + 3 * b, o(6)), l(F, V[0], V[1], V[2], p + 2 * f, x + 3 * b, o(7)), l(F, m[0], m[1], m[2], p + f, x + 3 * b, o(8)), l(F, A[0], A[1], A[2], p, x + 3 * b, o(9)), l(F, j[0], j[1], j[2], p, x + 2 * b, o(10)), l(F, q[0], q[1], q[2], p, x + b, o(11)), l(F, D[0], D[1], D[2], p, x, o(12)) } function l(t, e, a, r, n, s, l) { var o, i = h / 30 + "px", p = h / 24 + "px", x = t.append("text").text(e).attr("font-size", i).node().getBoundingClientRect(); o = x.height; var u = n + .1 * b, g = s + .25 * f, d = u, y = g + o + .1 * f, c = u, k = y + .25 * f; e.length > 1 && t.append("text").text(e).attr("font-size", i).attr("x", u).attr("y", g).style("fill", z.planetColor), a.length > 1 && t.append("text").text(a).attr("font-size", i).attr("x", d).attr("y", y).style("fill", z.planetColor), r.length > 1 && t.append("text").text(r).attr("font-size", i).attr("x", c).attr("y", k).style("fill", z.planetColor), l && (l && r.length > 1 ? t.append("text").text(r + " Asc").attr("font-size", i).attr("x", c).attr("y", k).style("fill", z.ascendantColor) : t.append("text").text("Asc").attr("font-size", p).attr("x", c).attr("y", k).style("fill", z.ascendantColor)) } function o(t) { return t == E } function i() { for (var t = 0; t < S.length; t++) { var e = S[t]; switch (null != e && e.length > 0 || (e = " "), t) { case 0: if (e.length > 14) { var a = e.substring(0, 12), r = e.substring(12, 26), n = e.substring(26, e.length); J[0] = a, J[1] = r } else J[0] = e, J[1] = "", J[2] = ""; break; case 1: if (e.length > 14) { var a = e.substring(0, 12), r = e.substring(12, 26), n = e.substring(26, e.length); K[0] = a, K[1] = r, K[2] = n } else K[0] = e, K[1] = "", K[2] = ""; break; case 2: if (e.length > 14) { var a = e.substring(0, 12), r = e.substring(12, 26), n = e.substring(26, e.length); R[0] = a, R[1] = r, R[2] = n } else R[0] = e, R[1] = "", R[2] = ""; break; case 3: if (e.length > 14) { var a = e.substring(0, 12), r = e.substring(12, 26), n = e.substring(26, e.length); B[0] = a, B[1] = r, B[2] = n } else B[0] = e, B[1] = "", B[2] = ""; break; case 4: if (e.length > 14) { var a = e.substring(0, 12), r = e.substring(12, 26), n = e.substring(26, e.length); N[0] = a, N[1] = r, N[2] = n } else N[0] = e, N[1] = "", N[2] = ""; break; case 5: if (e.length > 14) { var a = e.substring(0, 12), r = e.substring(12, 26), n = e.substring(26, e.length); P[0] = a, P[1] = r, P[2] = n } else P[0] = e, P[1] = "", P[2] = ""; break; case 6: if (e.length > 14) { var a = e.substring(0, 12), r = e.substring(12, 26), n = e.substring(26, e.length); V[0] = a, V[1] = r, V[2] = n } else V[0] = e, V[1] = "", V[2] = ""; break; case 7: if (e.length > 14) { var a = e.substring(0, 12), r = e.substring(12, 26), n = e.substring(26, e.length); m[0] = a, m[1] = r, m[2] = n } else m[0] = e, m[1] = "", m[2] = ""; break; case 8: if (e.length > 14) { var a = e.substring(0, 12), r = e.substring(12, 26), n = e.substring(26, e.length); A[0] = a, A[1] = r, A[2] = n } else A[0] = e, A[1] = "", A[2] = ""; break; case 9: if (e.length > 14) { var a = e.substring(0, 12), r = e.substring(12, 26), n = e.substring(26, e.length); j[0] = a, j[1] = r, j[2] = n } else j[0] = e, j[1] = "", j[2] = ""; break; case 10: if (e.length > 14) { var a = e.substring(0, 12), r = e.substring(12, 26), n = e.substring(26, e.length); q[0] = a, q[1] = r, q[2] = n } else q[0] = e, q[1] = "", q[2] = ""; break; case 11: if (e.length > 14) { var a = e.substring(0, 12), r = e.substring(12, 26), n = e.substring(26, e.length); D[0] = a, D[1] = r, D[2] = n } else D[0] = e, D[1] = "", D[2] = "" } } s(G, H) } var p, x, h, u, g, d, y, f, b, c, k, C, M, S, v = [], w = [], z = { lineColor: "black", planetColor: "black", ascendantColor: "blue", width: 350 }, J = ["Su Mo Ve Ma", "Sa Ju Ra Ke", "Ne Pl"], K = ["Su Mo Ve Ma", "Sa Ju Ra Ke", "Ne Pl"], R = ["Su Mo Ve Ma", "Sa Ju Ra Ke", "Ne Pl"], B = ["Su Mo Ve Ma", "Sa Ju Ra Ke", "Ne Pl"], N = ["Su Mo Ve Ma", "Sa Ju Ra Ke", "Ne Pl"], P = ["Su Mo Ve Ma", "Sa Ju Ra Ke", "Ne Pl"], V = ["Su Mo Ve Ma", "Sa Ju Ra Ke", "Ne Pl"], m = ["Su Mo Ve Ma", "Sa Ju Ra Ke", "Ne Pl"], A = ["Su Mo Ve Ma", "Sa Ju Ra Ke", "Ne Pl"], j = ["Su Mo Ve Ma", "Sa Ju Ra Ke", "Ne Pl"], q = ["Su Mo Ve Ma", "Sa Ju Ra Ke", "Ne Pl"], D = ["Su Mo Ve Ma", "Sa Ju Ra Ke", "Ne Pl"], E = 4; S = [["Su Mo Me Ma Ju Sa Ra Ke"], ["Su Mo Me Ma Ju Sa Ra Ke"], ["Su Mo Me Ma Ju Sa Ra Ke"], ["Su Mo Me Ma Ju Sa Ra Ke"], ["Su Mo Me Ma Ju Sa Ra Ke"], ["Su Mo Me Ma Ju Sa Ra Ke"], ["Su Mo Me Ma Ju Sa Ra Ke"], ["Su Mo Me Ma Ju Sa Ra Ke"], ["Su Mo Me Ma Ju Sa Ra Ke"], ["Su Mo Me Ma Ju Sa Ra Ke"], ["Su Mo Me Ma Ju Sa Ra Ke"], ["Su Mo Me Ma Ju Sa Ra Ke"]], t && (S = t), e && (E = e), a && (a.width && (z.width = a.width), a.lineColor && (z.lineColor = a.lineColor), a.planetColor && (z.planetColor = a.planetColor), a.ascendantColor && (z.ascendantColor = a.ascendantColor)), n(z.width); var F, G = h, H = u; i() }