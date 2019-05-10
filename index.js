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
				this.$events.$on("model.update", this.refresh);
			},
			destroyed: function() {
				this.$events.$off("model.update", this.refresh);
			},
			template: `
				<section class="k-pings-section">
					<header class="k-section-header">
						<k-headline>{{ headline }}</k-headline>
					</header>
					<k-text><span v-html="pings"></span></k-text>
				</section>
			`,
			methods: {
				refresh(){
					this.load().then(response => {
						this.pings    = response.pings;
					});
				}
			}
		}
	}	

});
