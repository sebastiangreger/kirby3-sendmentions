panel.plugin('sgkirby/sendmentions', {

	sections: {
		pings: {
			data: function () {
				return {
					headline: null,
					pings: null
				}
			},
			created: function() {
				this.load().then(response => {
					this.headline = response.headline;
					this.pings    = response.pings;
				});
			},
			template: `
				<section class="k-pings-section">
					<k-headline>{{ headline }}</k-headline>
					<k-text><span v-html="pings"></span></k-text>
				</section>
			`
		}
	}	

});
