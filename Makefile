all:
	if [[ -e bitrix-tarlanpayments.zip ]]; then rm bitrix-tarlanpayments.zip; fi
	if [[ -e bitrix-tarlanpayments-windows-1251.zip ]]; then rm bitrix-tarlanpayments-windows-1251.zip; fi
	zip -r bitrix-tarlanpayments.zip tarlan.payments
	find tarlan.payments -name \*.php -exec sh -c 'iconv -f utf-8 -t cp1251 {} > {}.1251 && mv {}.1251 {}' \;
	zip -r bitrix-tarlanpayments-windows-1251.zip tarlan.payments
	mv tarlan.payments .last_version
	zip -r last_version.zip .last_version
	mv .last_version tarlan.payments
	git checkout -f tarlan.payments
	find tarlan.payments -name \*.php.1251 -exec sh -c 'rm -f {}' \;
