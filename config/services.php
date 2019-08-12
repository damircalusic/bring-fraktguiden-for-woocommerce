<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

return [
	'common'  => [
		'title'       => 'Services',
		'description' => '',
		'expanded'    => true,
		'services'    => [
			'NORGESPAKKE'    => [
				'ProductCode'     => '3067',
				'productName'     => 'Norgespakke egenemballert',
				'displayName'     => 'På postkontor eller post i butikk (Norgespakke)',
				'DescriptionText' => 'Hentes på mottakers lokale postkontor/post i butikk.',
				'HelpText'        => 'Sendingen er en Norgespakke som blir levert til mottakers postkontor/ post i butikk. Mottaker varsles om at sendingen er ankommet via hentemelding i postkassen. Transporttid er normalt 2-3 virkedager, avhengig av strekning. Sendingen kan spores ved hjelp av sporingsnummeret.',
			],
			'SERVICEPAKKE'   => [
				'ProductCode'     => '1202',
				'productName'     => 'Klimanøytral Servicepakke',
				'displayName'     => 'På posten',
				'DescriptionText' => 'Hentes på mottakers lokale postkontor/post i butikk.',
				'HelpText'        => 'Sendingen er en Klimanøytral Servicepakke som blir levert til mottakers postkontor/ post i butikk. Mottaker kan velge å hente sendingen på et annet postkontor/post i butikk enn sitt lokale. Mottaker varsles om at sendingen er ankommet via SMS, e-post eller hentemelding i postkassen. Transporttid er normalt 1-3 virkedager, avhengig av strekning. Sendingen kan spores ved hjelp av sporingsnummeret.',
			],
			'PA_DOREN'       => [
				'ProductCode'     => '1736',
				'productName'     => 'På Døren',
				'displayName'     => 'Hjem på kvelden, 17-21',
				'DescriptionText' => 'Pakken leveres hjem til deg, sjåføren ringer 30 - 60 min. før ankomst',
				'HelpText'        => 'Sendingen leveres hjem til deg mellom klokken 17 og 21. Du varsles i god tid om forventet utleveringsdag på sms og/eller e-post, i tillegg til nytt varsel når sendingen er lastet på bil for utkjøring samme kveld. Sjåfør ringer deg på mobiltelefon 30 - 60 minutter før levering. Dersom sendingen ikke kan leveres, blir den fraktet til lokalt postkontor/ post i butikk og du vil motta en varsel om dette via SMS, e-post eller hentemelding i postkassen. Sendingen kan spores ved hjelp av sporingsnummeret.',
			],
			'MAIL'           => [
				'ProductCode'     => '',
				'productName'     => 'Brev',
				'displayName'     => 'Brev',
				'DescriptionText' => '',
				'HelpText'        => '',
			],
			'CARGO_GROUPAGE' => [
				'ProductCode'     => '3050',
				'productName'     => 'Cargo',
				'displayName'     => 'Cargo',
				'DescriptionText' => '',
				'HelpText'        => '',
			],
		],
	],
	'mailbox' => [
		'title'       => 'Mailbox',
		'description' => __( 'Packages up to 2 kg.', 'bring-fraktguiden-for-woocommerce' ) . PHP_EOL . '<strong style="color:#C00">' . __( 'Please note in order to use mailbox with tracking you need a RFID printer.', 'bring-fraktguiden-for-woocommerce' ) . '</strong>',
		'expanded'    => true,
		'services'    => [
			/**
			 * Pakke i postkassen
			 * A customer number is required for ordering Pakke i postkassen services. There is a minimum fee of 150,- for each order.
			 */
			'PAKKE_I_POSTKASSEN'         => [
				'ProductCode'     => '3584',
				'productName'     => 'Pakke i postkassen',
				'displayName'     => 'Pakke i postkassen',
				'DescriptionText' => 'Pakken leveres i din postkasse innen 2 virkedager.',
				'HelpText'        => 'Pakke i postkassen leveres i mottakers postkasse, og er egnet for små og lette sendinger (maksimalt 2 kg). Dersom postkassen er låst eller full, blir pakken sendt til mottakers lokale hentested (postkontontor eller Post i Butikk).',
				'ProductLink'     => 'https://www.bring.no/sende/pakker/private-i-norge/pakke-i-postkassen',
			],
			'PAKKE_I_POSTKASSEN_SPORBAR' => [
				'ProductCode'     => '',
				'productName'     => 'Pakke i postkassen (sporbar)',
				'displayName'     => 'Pakke i postkassen (sporbar)',
				'DescriptionText' => 'Pakken leveres i din postkasse innen 2 virkedager.',
				'HelpText'        => 'Pakke i postkassen leveres i mottakers postkasse, og er egnet for små og lette sendinger (maksimalt 2 kg). Dersom postkassen er låst eller full, blir pakken sendt til mottakers lokale hentested (postkontontor eller Post i Butikk).',
				'ProductLink'     => 'https://www.bring.no/sende/pakker/private-i-norge/pakke-i-postkassen',
			],
		],
	],
	'other'   => [
		'title'       => 'Other services',
		/* translators: %s: HTML tags */
		'description' => '<strong style="color:#C00">' . sprintf( __( 'Cargo services do not have unique %1$sProduct names%2$s.', 'bring-fraktguiden-for-woocommerce' ), '<em>', '</em>' ) . PHP_EOL . sprintf( __( 'Switch the %1$sDisplay Service As%2$s to %3$sDisplay names%4$s to ensure customers can see the correct service.', 'bring-fraktguiden-for-woocommerce' ), '<u>', '</u>', '<em>', '</em>' ) . '</strong>',
		'expanded'    => false,
		'services'    => [
			'BPAKKE_DOR-DOR'                => [
				'ProductCode'     => '1000',
				'productName'     => 'Bedriftspakke',
				'displayName'     => 'På jobben, 08-16',
				'DescriptionText' => 'Leveres uten at sjåføren ringer først',
				'HelpText'        => 'Sendingen er en Bedriftspakke som leveres til mottakers arbeidssted mellom klokken 08 og 16. Bestiller du varsling, vil mottaker varsles når sendingen er lastet på bil for uttkjøring, via SMS og/eller e-post. Dersom sendingen ikke kan leveres, blir den fraktet til lokalt postkontor/ post i butikk. Mottaker varsles om dette via SMS, e-post eller hentemelding i postkassen. Sendingen kan spores ved hjelp av sporingssnummeret.',
			],
			'EKSPRESS09'                    => [
				'ProductCode'     => '1002',
				'productName'     => 'Bedriftspakke Ekspress-Over natten',
				'displayName'     => 'Ekspress over natten',
				'DescriptionText' => 'Levering på dør vil skje påfølgende dag innen kl 1600 for dette postnummeret.',
				'HelpText'        => 'Levering hjem på dør før kl 0900 (til mindre steder normalt før kl 1600). Du kan varsles ved SMS/e-post, forutsatt at du har oppgitt telefonnummer/epostadresse ved bestilling. Sendingen kan spores ved hjelp av sporingsnummeret.',
			],
			'EXPRESS_NORDIC_SAME_DAY'       => [
				'ProductCode'     => '3336',
				'productName'     => 'Quickpack SameDay',
				'displayName'     => 'Omgående levering, dagtid og kveldstid',
				'DescriptionText' => 'Bud henter og leverer omgående til dør.',
				'HelpText'        => 'Når du trenger raskest mulig budlevering. Bring henter pakken omgående hos avsender og flyr den med første fly til mottaker. Innenfor Norden inkluderer produktet også forsikring av forsendelsen som dekker inntil NOK 100 000,- per sending.',
			],
			'EXPRESS_INTERNATIONAL_0900'    => [
				'ProductCode'     => '3337',
				'productName'     => 'Quickpack Over Night 0900',
				'displayName'     => 'Levering neste dag innen kl. 9',
				'DescriptionText' => 'Bud henter og leverer til dør.',
				'HelpText'        => 'Når du trenger raskest mulig budlevering. Bring henter pakken hos avsender og leverer den på dør til mottaker.',
			],
			'EXPRESS_INTERNATIONAL_1200'    => [
				'ProductCode'     => '3338',
				'productName'     => 'Quickpack Over Night 1200',
				'displayName'     => 'Levering neste dag innen kl. 12',
				'DescriptionText' => 'Bud henter og leverer til dør.',
				'HelpText'        => 'Når du trenger rask budlevering. Bring henter pakken hos avsender og leverer den på dør til mottaker.',
			],
			'EXPRESS_INTERNATIONAL'         => [
				'ProductCode'     => '3339',
				'productName'     => 'Quickpack Day Certain',
				'displayName'     => 'Levering neste dag',
				'DescriptionText' => 'Bud henter og leverer til dør.',
				'HelpText'        => 'Når du trenger en rask og rimelig budlevering. Bring henter pakken hos avsender og leverer den på dør til mottaker.',
			],
			'EXPRESS_ECONOMY'               => [
				'ProductCode'     => '3340',
				'productName'     => 'Quickpack Express Economy',
				'displayName'     => 'Levering tidligst neste dag kl. 17',
				'DescriptionText' => 'Bud henter og leverer til dør.',
				'HelpText'        => 'Når du trenger rimelig budlevering. Bring henter pakken hos avsender og leverer den på dør til mottaker.',
			],

			/**
			 * Business parcel
			 * Fuel surcharge is not included in the price returned from the ShippingGuide and must be calculated / added manually. Bring Parcels is entitled to change this charge without notice to the customer. Any fuel surcharge applied will be detailed on the invoice.
			 */
			'BUSINESS_PARCEL'               => [
				'ProductCode'     => '0330',
				'productName'     => 'CarryOn Business',
				'displayName'     => 'Til mottakers dør',
				'DescriptionText' => 'Pakke til bedrifter i utlandet',
				'HelpText'        => 'CarryOn Business er en enkelt og effektiv måte å sende pakker til andre firmaer i Norden og til resten av verden. Pakker hentes i henhold til avtale, og leveres til mottaker mellom mandag- fredag i kontortiden. I enkelte land leveres pakkene på mottakers postkontor.',
			],

			/**
			 * PickUp Parcel
			 * Fuel surcharge is not included in the price returned from the ShippingGuide and must be calculated / added manually. Bring Parcels is entitled to change this charge without notice to the customer. Any fuel surcharge applied will be detailed on the invoice.
			 * For shipments to Denmark, PICKUP_PARCEL needs to be ordered with the “PICKUP_POINT” additional service to send as a low-cost delivery from Bring’s parcel shops or parcel lockers. Otherwise the shipment will be sent as a more expensive home delivery.
			 */
			'PICKUP_PARCEL'                 => [
				'ProductCode'     => '0340',
				'productName'     => 'CarryOn HomeShopping',
				'displayName'     => 'Till utlämningsställe',
				'DescriptionText' => 'Hentes på mottakers lokale utleveringssted i butikk.',
				'HelpText'        => 'Sendingen blir levert til mottakers nærmeste utleveringssted. Mottaker kan velge å hente sendingen på et annet postkontor/post i butikk enn sitt lokale. Mottaker varsles om at sendingen er ankommet via SMS, e-post eller hentemelding i postkassen. Sendingen kan spores ved hjelp av sporingsnummeret.',
			],

			'COURIER_VIP'                   => [
				'ProductCode'     => 'VIP25',
				'productName'     => 'Bud VIP',
				'displayName'     => 'Omgående levering',
				'DescriptionText' => 'Leveres omgående av bud til dør.',
				'HelpText'        => 'Sending hentes hos avsender innen 10 minutter og leveres direkte til mottaker.',
			],
			'COURIER_1H'                    => [
				'ProductCode'     => '1H25',
				'productName'     => 'Bud 1 time',
				'displayName'     => 'Levering innen 1 time',
				'DescriptionText' => 'Leveres innen 1 time av bud til dør.',
				'HelpText'        => 'Sending hentes hos avsender og leveres til mottaker innen 1 time.',
			],
			'COURIER_2H'                    => [
				'ProductCode'     => '2H25',
				'productName'     => 'Bud 2 timer',
				'displayName'     => 'Levering innen 2 timer',
				'DescriptionText' => 'Leveres av bud til dør innen 2 timer.',
				'HelpText'        => 'Sending hentes hos avsender og leveres til mottaker innen 2 timer.',
			],
			'COURIER_4H'                    => [
				'ProductCode'     => '4H25',
				'productName'     => 'Bud 4 timer',
				'displayName'     => 'Levering innen 4 timer',
				'DescriptionText' => 'Leveres av bud til dør innen 4 timer.',
				'HelpText'        => 'Sending hentes hos avsender og leveres til mottaker innen 4 timer.',
			],
			'COURIER_6H'                    => [
				'ProductCode'     => '6H25',
				'productName'     => 'Bud 6 timer',
				'displayName'     => 'Levering innen 6 timer',
				'DescriptionText' => 'Leveres av bud til dør innen 6 timer.',
				'HelpText'        => 'Sending hentes hos avsender og leveres til mottaker innen 6 timer.',
			],

			/**
			 * Oil Express products can be shipped only in Norway and between certain postal codes.
			 */
			'OIL_EXPRESS'                   => [
				'ProductCode'     => '3050',
				'productName'     => 'OIL_EXPRESS',
				'displayName'     => 'Oil Express',
				'DescriptionText' => 'N/A',
				'HelpText'        => 'Oil Express products can be shipped only in Norway and between certain postal codes.',
			],

			/**
			 * Frigo
			 *
			 * Frigo products can be ordered only in Norway and requires a Frigo customer.
			 */
			'FRIGO'                         => [
				'ProductCode'     => '',
				'productName'     => 'FRIGO',
				'displayName'     => 'Frigo',
				'DescriptionText' => 'N/A',
				'HelpText'        => 'Frigo products can be ordered only in Norway and requires a Frigo customer.',
			],


			/**
			 * Home Delivery Product List
			 * Lead time given is from first Bring terminal to the terminal delivering
			 * the goods. Local cut-off times are not accounted for, so the client should
			 * adjust for these. These products are only available when accessed with a
			 * PARCELS_NORWAY customer number tied to an appropriate agreement.
			 */
			'SINGLE_INDOOR'                 => [
				'ProductCode'     => '',
				'productName'     => 'OIL_EXPRESS',
				'displayName'     => 'Single Indoor',
				'DescriptionText' => 'N/A',
				'HelpText'        => '',
			],
			'CURBSIDE'                      => [
				'ProductCode'     => '',
				'productName'     => 'OIL_EXPRESS',
				'displayName'     => 'Curbside ',
				'DescriptionText' => 'N/A',
				'HelpText'        => '',
			],
			'CURBSIDE_EXPRESS'              => [
				'ProductCode'     => '',
				'productName'     => 'OIL_EXPRESS',
				'displayName'     => 'Curbside Express ',
				'DescriptionText' => 'N/A',
				'HelpText'        => '',
			],
			'CURBSIDE_WEEKEND'              => [
				'ProductCode'     => '',
				'productName'     => 'OIL_EXPRESS',
				'displayName'     => 'Curbside Weekend ',
				'DescriptionText' => 'N/A',
				'HelpText'        => '',
			],
			'CURBSIDE_WEEKEND_EXPRESS'      => [
				'ProductCode'     => '',
				'productName'     => 'OIL_EXPRESS',
				'displayName'     => 'Curbside Weekend Express ',
				'DescriptionText' => 'N/A',
				'HelpText'        => '',
			],
			'CURBSIDE_EVENING'              => [
				'ProductCode'     => '',
				'productName'     => 'OIL_EXPRESS',
				'displayName'     => 'Curbside Evening ',
				'DescriptionText' => 'N/A',
				'HelpText'        => '',
			],
			'DOUBLE_INDOOR'                 => [
				'ProductCode'     => '',
				'productName'     => 'OIL_EXPRESS',
				'displayName'     => 'Double Indoor',
				'DescriptionText' => 'N/A',
				'HelpText'        => '',
			],
			'INDOOR_LIGHT'                  => [
				'ProductCode'     => '',
				'productName'     => 'OIL_EXPRESS',
				'displayName'     => 'Indoor Light ',
				'DescriptionText' => 'N/A',
				'HelpText'        => '',
			],
			'SINGLE_INDOOR_EXPRESS'         => [
				'ProductCode'     => '',
				'productName'     => 'OIL_EXPRESS',
				'displayName'     => 'Single Indoor Express',
				'DescriptionText' => 'N/A',
				'HelpText'        => '',
			],
			'DOUBLE_INDOOR_EXPRESS'         => [
				'ProductCode'     => '',
				'productName'     => 'OIL_EXPRESS',
				'displayName'     => 'Double Indoor Express',
				'DescriptionText' => 'N/A',
				'HelpText'        => '',
			],
			'INDOOR_LIGHT_EXPRESS'          => [
				'ProductCode'     => '',
				'productName'     => 'OIL_EXPRESS',
				'displayName'     => 'Indoor Light Express ',
				'DescriptionText' => 'N/A',
				'HelpText'        => '',
			],
			'SINGLE_INDOOR_WEEKEND'         => [
				'ProductCode'     => '',
				'productName'     => 'OIL_EXPRESS',
				'displayName'     => 'Single Indoor Weekend',
				'DescriptionText' => 'N/A',
				'HelpText'        => '',
			],
			'DOUBLE_INDOOR_WEEKEND'         => [
				'ProductCode'     => '',
				'productName'     => 'OIL_EXPRESS',
				'displayName'     => 'Double Indoor Weekend',
				'DescriptionText' => 'N/A',
				'HelpText'        => '',
			],
			'INDOOR_LIGHT_WEEKEND'          => [
				'ProductCode'     => '',
				'productName'     => 'OIL_EXPRESS',
				'displayName'     => 'Indoor Light Weekend ',
				'DescriptionText' => 'N/A',
				'HelpText'        => '',
			],
			'SINGLE_INDOOR_WEEKEND_EXPRESS' => [
				'ProductCode'     => '',
				'productName'     => 'OIL_EXPRESS',
				'displayName'     => 'Single Indoor Weekend Express',
				'DescriptionText' => 'N/A',
				'HelpText'        => '',
			],
			'DOUBLE_INDOOR_WEEKEND_EXPRESS' => [
				'ProductCode'     => '',
				'productName'     => 'OIL_EXPRESS',
				'displayName'     => 'Double Indoor Weekend Express',
				'DescriptionText' => 'N/A',
				'HelpText'        => '',
			],
			'INDOOR_LIGHT_WEEKEND_EXPRESS'  => [
				'ProductCode'     => '',
				'productName'     => 'OIL_EXPRESS',
				'displayName'     => 'Indoor Light Weekend Express ',
				'DescriptionText' => 'N/A',
				'HelpText'        => '',
			],
			'HOME_DELIVERY_RETURN'          => [
				'ProductCode'     => '',
				'productName'     => 'OIL_EXPRESS',
				'displayName'     => 'Return Home Delivery ',
				'DescriptionText' => 'N/A',
				'HelpText'        => '',
			],
			'DOUBLE_INDOOR_NO'              => [
				'ProductCode'     => '',
				'productName'     => 'OIL_EXPRESS',
				'displayName'     => 'Double Indoor (NO) ',
				'DescriptionText' => 'N/A',
				'HelpText'        => '',
			],
			'SINGLE_INDOOR_EVENING'         => [
				'ProductCode'     => '',
				'productName'     => 'OIL_EXPRESS',
				'displayName'     => 'Single Indoor Evening',
				'DescriptionText' => 'N/A',
				'HelpText'        => '',
			],
			'DOUBLE_INDOOR_EVENING'         => [
				'ProductCode'     => '',
				'productName'     => 'OIL_EXPRESS',
				'displayName'     => 'Double Indoor Evening',
				'DescriptionText' => 'N/A',
				'HelpText'        => '',
			],
			'INDOOR_LIGHT_EVENING'          => [
				'ProductCode'     => '',
				'productName'     => 'OIL_EXPRESS',
				'displayName'     => 'Indoor Light Evening ',
				'DescriptionText' => 'N/A',
				'HelpText'        => '',
			],
		],
	],
];
