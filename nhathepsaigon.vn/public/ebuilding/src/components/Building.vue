<template>
  <div class="right_col dashboard_nav" role="main" style="min-height: 588px;">
    <div class="">
      <div class="row">
        <div class="col-md-12">
          <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12 text-center">
              <div class="btn_them_can_ho">
                <a class="btn btn-success" href="javascript:void(0)" v-on:click="openFormNewBuilding()">Thêm Tòa Nhà</a>
              </div>
            </div>
          </div>
          <div class="x_panel">
            <div class="x_content">
              <div class="col-md-4 col-sm-4 col-xs-12 profile_details" v-for="building in buildings">
                <a href="javascript:void(0)" v-on:click="chooseBuilding(building)">
                  <div class="well profile_view">
                    <div class="col-sm-12">
                      <div class="left col-xs-7">
                        <h2>{{building.name}}</h2>
                        <ul class="list-unstyled building-ul">
                          <li><i class="fa fa-building"></i> {{building.cmp[0].name}}</li>
                          <li><i class="fa fa-map-marker"></i> {{building.address}}</li>
                        </ul>
                      </div>
                      <div class="right col-xs-5 text-center">
                        <img v-bind:src="building.logo" alt="" class="img-circle img-responsive">
                      </div>
                    </div>
                  </div>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <modal name="them-toa-nha" v-model="openFormBuilding" title="Thêm tòa nhà" @hide="closeFormNewBuilding" size="lg" :backdrop="false">
        <div>
          <form class="form-horizontal form-label-left">
            <div class="form-group">
              <label class="control-label col-md-3" for="building-name">Tên tòa nhà <span class="required">*</span>
              </label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" id="building-name" v-model="newBuilding.name" required="required" class="form-control col-md-7 col-xs-12">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3" for="address">Địa chỉ <span class="required">*</span>
              </label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" id="address" name="address" v-model="newBuilding.address" required="required" class="form-control col-md-7 col-xs-12">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3" for="investor">Chủ đầu tư</label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" id="investor" name="investor" v-model="newBuilding.investor" required="required" class="form-control col-md-7 col-xs-12">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3" for="investor-company">Địa chỉ công ty đầu tư</label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" id="investor-company" name="investor-company" v-model="newBuilding.investorAddress" required="required" class="form-control col-md-7 col-xs-12">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3" for="company">Loại tòa nhà</label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <select id="company" class="form-control" required v-model="newBuilding.kind">
                  <option value="Chung cư trung bình">Chung cư trung bình</option>
                  <option value="Tòa nhà">Tòa nhà</option>
                  <option value="Căn hộ">Căn hộ</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3" for="buget">Ngân quỹ</label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" id="buget" name="buget" v-model="newBuilding.bank" class="form-control col-md-7 col-xs-12">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3" for="note">Ghi chú hóa đơn</label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <textarea id="note" class="form-control" name="note" v-model="newBuilding.invoiceNote" ></textarea>
              </div>
            </div>
          </form>
        </div>
        <div slot="footer">
          <div class="prolabel fullw"><span class="error" v-if="error.msg">{{error.msg}}</span></div>
          <button type="button" class="btn btn-default" @click.prevent="closeFormNewBuilding('ok')">Tạo mới</button>
        </div>
    </modal>
  </div>
</template>

<script>
  import axios from './../client'
  export default {
    name: 'Building',
    data() {
      return {
        openFormBuilding: false,
        buildings: [],
        newBuilding: {},
        error: {msg: ``},
        processing: false
      }
    },
    mounted (){
      this.$store.commit('setBuilding', false)
    },
    created () {
      if(this.$route.query.ft){
        this.toggleBodyClass('removeClass', 'login')
        this.toggleBodyClass('addClass', 'nav-md')
      }
      this.listBuilding()
    },
    methods: {
      toggleBodyClass(addRemoveClass, className) {
        const el = document.body
        if (addRemoveClass === 'addClass') {
          el.classList.add(className)
        } else {
          el.classList.remove(className)
        }
      },
      listBuilding() {
        axios.get(`/building`).then(response => {
          this.buildings = response.data.buildings
        })
      },
      chooseBuilding(building){
        axios.post('/choose-building', {buildingId: building._id}).then(response => {
          let user = this.$store.state.user
          user.building = response.data.building._id
          this.$store.commit('setUser', user)
          this.$store.commit('setBuilding', true)
          this.$router.replace({name: 'can-ho'})
          //this.$router.replace({name: 'Dashboard'})
        })
      },
      openFormNewBuilding(){
        this.openFormBuilding = true
      },
      closeFormNewBuilding(event){
        if(event === 'ok'){
          if(this.processing) return
          this.processing = true
          if(!this.newBuilding || !this.newBuilding.name){
            this.error.msg = `Vui lòng nhập tên tòa nhà`
            this.processing = false
            return
          }
          if(!this.newBuilding.address){
            this.error.msg = `Vui lòng nhập địa chỉ tòa nhà`
            this.processing = false
            return
          }
          if(!this.newBuilding.kind){
            this.error.msg = `Vui lòng chọn loại tòa nhà`
            this.processing = false
            return
          }
          axios.post(`/building`, {building: this.newBuilding}).then(response => {
            this.processing = false
            if(response.data.s === 200){
              console.log(response.data.building)
              this.buildings.push(response.data.building)
              this.openFormBuilding = false
              this.newBuilding = {}
            } else {
              this.error.msg = response.data.msg
            }
          })
        } else {
          //cancel, dismiss
          this.newBuilding = {}
        }
      }
    }
  }
</script>

<style scoped>
  .error {
    color: red;
    float: left;
    font-size: initial;
  }
</style>
