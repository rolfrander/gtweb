---
title: "Har du lyst til å spille i korps?"
menu:
  main:
    weight: 10
    name: Bli medlem

description: Hos oss får du god musikkundervisning, gode venner og mange hyggelige og spennende opplevelser.
forsidebilde: /images/marsjering.jpg
bilde: /images/marsjering.jpg

excludeFirstPage: true

script:
 - /assets/js/medlem.js
 - https://www.google.com/recaptcha/api.js
---

Godlia-Trasop musikkorps består av ca. 60 musikkglade barn fordelt på aspirantkorps, juniorkorps og hovedkorps.

Meld deg inn i korpset hvis du vil:

- lære å spille et instrument
- blir god med dyktige dirigenter og instruktører
- ha det gøy med andre
- spille med andre
- få enetimer med instruktør
- delta på seminarer og korpsturer

{{<button link="#form" tekst="trykk her for innmeldingsskjema" >}}

## Hvordan er det å gå i korps?

Korpset har opptak en gang i året, på høsten.

Nybegynnerne starter i aspirantkorpset, og går der i ett år. Deretter rykker de opp til juniorkorpset, hvor de går i to år. Så rykker de opp til hovedkorpset, der de kan spille til de har fylt 19 år. I korpset får hvert medlem en enetime med instruktør en gang i uken. I tillegg har alle aspiranter, alle juniorkorpsmedlemmer og alle hovedkorpsmedlemmer fellesøving en gang i uken.

Alle våre instruktører er profesjonelle og dyktige.

Det å spille i korps skal være en aktivitet for alle, både lærerikt og hyggelig.

Noen av musikantene har egne instrumenter, men de aller fleste låner av korpset. Vi forsøker så langt det er mulig å etterkomme musikantenes instrumentønsker.

Korpsets musikanter spiller bl.a.:

- Fløyte
- Klarinett
- Horn
- Kornett
- Baryton
- Tuba
- Trombone
- Saxofon
- Slagverk (trommer m.m.)

## Innmelding

Vi ønsker oss mange nye musikanter, så har *du* lyst til å spille i korps, trykk på knappen under. Kontingent er kr 2000 per halvår, til sammen 4000 kr per år. Merk at eventuell utmelding av korpset må skje før påbegynt semester/halvår. Kontingent for påbegynt halvår refunderes ikke.

Barnet meldes inn i Norges Musikkorps Forbund samtidig som det tas opp som medlem av Godlia/Trasop skolers musikkorps.

Hvis du lurer på noe kan du ta kontakt med oss på epost
{{<email medlem>}}

{{<button link="#form" tekst="trykk her for innmeldingsskjema" >}}

## Kjære foreldre
Mange barn har lyst til å spille i korps, og mange foreldre sier nei. Det er en utbredt myte at det er veldig mye arbeid å være korpsforelder. Vi vil hevde at det ikke stemmer - i antall timer bruker du antagelig vel så mye tid på å følge opp idrettsaktiviteter. Men vi har samlet dugnadsoppgavene rundt noen få hendelser i året, slik at du slipper å stå opp tidlig helg etter helg og tilbringe dagen i en idretthall eller på en forblåst slette. Du slipper også hallvakter.

Vi har vår dugnadinnsats konsentrert rundt et årlig loppemarked i midten av oktober, og loppemarked annet hvert år på våren. Foreldreaktiviteter i forbindelse med loppemarkedene er

- hente lopper i forkant av loppemarkedene
- bidra med rigging før markedet
- bidra med salg under selve loppemarkedet

Loppemarkedene betyr litt arbeidsinnsats fra foreldre, men er aller mest en sosial og hyggelig begivenhet, hvor du blir bedre kjent med barnas venner og foreldrene deres. I tillegg til loppemarked har vi ansvaret for 17. mai arrangementet på Trasop skole annet hvert år. La ikke redselen for dugnadsarbeid stå i veien for ditt barns musikkglede, for du har intet å frykte. Vi har det veldig gøy.

<div id="form">
  <h2>Innmeldingsskjema</h2>
  <form>
    <input type="hidden" name="xssid" value="foo">
    <datalist id="instrumenter">
      <option value="Fløyte"/>
      <option value="Klarinett"/>
      <option value="Saxofon"/>
      <option value="Horn"/>
      <option value="Kornett/Trompet"/>
      <option value="Trombone"/>
      <option value="Baryton"/>
      <option value="Tuba"/>
      <option value="Slagverk"/>
      <option value="(ingen)"/>
    </datalist>
    <h3>Musikant</h3>
    <table class="headerleft">
      <tr>
        <th><label for="navn">For- og mellomnavn</label></th>
        <td><input type="text" name="navn" id="navn"></td>
      </tr>
      <tr>
        <th><label for="etternavn">Etternavn</label></th>
        <td><input type="text" name="etternavn" id="etternavn"></td>
      </tr>
      <tr>
        <th><label for="adresse">Adresse</label></th>
        <td><input type="text" name="adresse" id="adresse"></td>
      </tr>
      <tr>
        <th><label for="postnr">Postnr</label></th>
        <td>
          <input type="text" maxlength="4" size="4" name="postnr" id="postnr" oninput="poststed_event(event, 'poststed'); return false;">
          <input type="text" size="20" name="poststed" id="poststed"  disabled>
        </td>
      </tr>
      <tr>
        <th><label for="tlf">Telefon</label></th>
        <td><input type="tlf" name="tlf" id="tlf"></td>
      </tr>
      <tr><th colspan="2">Ønsket instrument</th></tr>
      <tr>
        <th><label for="instr1">Førstevalg</label></th>
        <td><input list="instrumenter" name="instr1" id="instr1"></td>
      </tr>
      <tr>
        <th><label for="instr2">Andrevalg</label></th>
        <td><input list="instrumenter" name="instr2" id="instr1"></td>
      </tr>
      <tr>
        <th><label for="instr3">Tredjevalg</label></th>
        <td><input list="instrumenter" name="instr3" id="instr1"></td>
      </tr>
    </table>
    <h3>Foresatt 1</h3>
    <table class="headerleft">
      <tr>
        <th><label for="f1_navn">Fornavn og mellomnavn</label></th>
        <td><input type="text" name="f1_navn" id="f1_navn"></td>
      </tr>
      <tr>
        <th><label for="f1_etternavn">Etternavn</label></th>
        <td><input type="text" name="f1_etternavn" id="f1_etternavn"></td>
      </tr>
      <tr>
        <th><label for="f1_epost">Epost</label></th>
        <td><input type="email" name="f1_epost" id="f1_epost"></td>
      </tr>
      <tr>
        <th><label for="f1_tel">Telefon</label></th>
        <td><input type="tel" name="f1_tel" id="f1_tel"></td>
      </tr>
      <tr>
        <th><label for="f1_adresse">Adresse (hvis forskjellig)</label></th>
        <td><input type="text" name="f1_adresse" id="f1_adresse"></td>
      </tr>
      <tr>
        <th><label for="f1_postnr">Postnr</label></th>
        <td>
          <input type="text" maxlength="4" size="4" name="f1_postnr" id="f1_postnr" oninput="poststed_event(event, 'f1_poststed'); return false;">
          <input type="text" size="20" name="f1_poststed" id="f1_poststed"  disabled>
        </td>
      </tr>
    </table>
    <h3>Foresatt 2</h3>
    <table class="headerleft">
      <tr>
        <th><label for="f2_navn">Fornavn og mellomnavn</label></th>
        <td><input type="text" name="f2_navn" id="f2_navn"></td>
      </tr>
      <tr>
        <th><label for="f2_etternavn">Etternavn</label></th>
        <td><input type="text" name="f2_etternavn" id="f2_etternavn"></td>
      </tr>
      <tr>
        <th><label for="f2_epost">Epost</label></th>
        <td><input type="email" name="f2_epost" id="f2_epost"></td>
      </tr>
      <tr>
        <th><label for="f2_tel">Telefon</label></th>
        <td><input type="tel" name="f2_tel" id="f2_tel"></td>
      </tr>
      <tr>
        <th><label for="f2_adresse">Adresse (hvis forskjellig)</label></th>
        <td><input type="text" name="f2_adresse" id="f2_adresse"></td>
      </tr>
      <tr>
        <th><label for="f2_postnr">Postnr</label></th>
        <td>
          <input type="text" maxlength="4" size="4" name="f2_postnr" id="f2_postnr" oninput="poststed_event(event, 'f2_poststed'); return false;">
          <input type="text" size="20" name="f2_poststed" id="f2_poststed"  disabled>
        </td>
      </tr>
    </table>
    <p id="feilmeldinger"/>
    <p>
      <div class="g-recaptcha" data-sitekey="6LeYE3UUAAAAAI30gDamV1G6fmxH5tvu6Etok0-M"></div>
      <input type="button" value="Send skjema" onclick="nytt_medlem(); return false;">
      <input type="button" value="Avbryt" onclick="hide('div#form'); return false;" >
    </p>
  </form>
</div>

<div id="result">
  <p>Takk for din registrering, søknads-id er <span id="soknadsid"/></p>
  <p>Hvis du lurer på noe, ta kontakt med <my-email data-user="medlem" data-domain="godliatrasop.no">@</my-email></p>
  <input type="button" value="Lukk" onclick="hide('div#result'); return false;" >
</div>

<div id="feil">
  <p>Det skjedde dessverre en feil ved registrering, ta kontakt med <my-email data-user="medlem" data-domain="godliatrasop.no">@</my-email> for mer info</p>
  <p>Feilkode: <span id="feilkode"/></p>
  <input type="button" value="Lukk" onclick="hide('div#feil'); return false;" >
</div>
