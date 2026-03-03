import $ from 'jquery'
import * as moment from 'moment'
import select2 from 'select2'
import 'daterangepicker'
import 'datatables.net-bs5'
import * as bootstrap from 'bootstrap'
import Dropzone from 'dropzone'

window.$ = window.jQuery = $
window.moment = moment
window.bootstrap = bootstrap
window.Dropzone = Dropzone

// Initialize select2 with jQuery
select2($)
