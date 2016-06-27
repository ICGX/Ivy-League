
        <input type="hidden" name="type" value="addUser">
        <div class="reg_box">
            <div class="reg_left">用戶名:</div>
            <div class="reg_right">
                <div>
                    <input type="text" name="username" required>
                </div>
            </div>
        </div><br/><br/>
        <div class="reg_box">
            <div class="reg_left">密&nbsp;碼:</div>
            <div class="reg_right">
                <div>
                    <input id="password" type="password" name="password" required>
                </div>
            </div>
        </div><br/><br/>
        <div class="reg_box">
            <div class="reg_left">確認密碼:</div>
            <div class="reg_right">
                <div>
                    <input id="confirm" type="password" name="confirm" required> 
                </div>
            </div>
        </div><br/><br/>
        <div class="reg_box">
            <div class="reg_left">姓&nbsp;名:</div>
            <div class="reg_right">
                <div>
                   <input type="text" name="name" required>
                </div>
            </div>
        </div><br/><br/>
        <div class="reg_box">
            <div class="reg_left">性&nbsp;別:</div>
            <div class="reg_right">
                <div>
                    <select name="sex">
                    <option value="nam">男</option>
                    <option value="women">女</option>
                    </select>
                </div>
            </div>
        </div><br/><br/>
        <div class="reg_box">
            <div class="reg_left">生日日期:</div>
            <div class="reg_right">
                <div>
                   <input type="date" name="borndate"  required>
                </div>
            </div>
        </div><br/><br/>
        <div class="reg_box">
            <div class="reg_left">年&nbsp;級:</div>
            <div class="reg_right">
                <div>
                    <select name="grade">
                        <option value="1">一年級</option>
                        <option value="2">二年級</option>
                        <option value="3">三年級</option>
                        <option value="4">四年級</option>
                        <option value="5">五年級</option>
                        <option value="6">六年級</option>
                  </select>
                </div>
            </div>
        </div><br/><br/>
        <div class="reg_box">
            <div class="reg_left">學科:</div>
            <div class="reg_right">
                <div>
                    <?php $subjects = User::get_subjects(); ?>
                    <?php foreach ($subjects as $subject): ?>
                        <p>
                            <input class="subject" type="checkbox" name="subject[]" value="<?php echo $subject['id'];?>">
                            <span><?php echo $subject['name']; ?></span>
                        </p>
                    <?php endforeach; ?>
                </div>
            </div>
        </div><br/><br/>
        <div class="reg_box">
            <div class="reg_left">電&nbsp;話:</div>
            <div class="reg_right">
                <div>
                    <input type="text" name="telephone" required>
                </div>
            </div>
        </div><br/><br/>
        <div class="reg_box">
            <div class="reg_left">地&nbsp;址:</div>
            <div class="reg_right">
                <div>
                   <input type="text" name="adress" required>
                </div>
            </div>
        </div><br/><br/> 
        <div class="reg_box">
            <div class="reg_left">電&nbsp;郵:</div>
            <div class="reg_right">
                <div>
                   <input type="email" name="email" required>
                </div>
            </div>
        </div><br/><br/>
        <div class="reg_box">
            <div class="reg_left">家長用戶名稱:</div>
            <div class="reg_right">
                <div>
                   <input type="text" name="parents" required>
                </div>
            </div>
        </div><br/><br/>
        <div class="reg_box">
            <div class="reg_left">權&nbsp;限:</div>
            <div class="reg_right">
                <div>
                    <select name="level">
                        <?php $levels = User::get_permission_list(); ?>
                        <?php foreach($levels as $level): ?>
                            <option value="<?php echo $level['level']; ?>"><?php echo $level['name']; ?></option>
                        <?php endforeach; ?>
                  </select>
                </div>
            </div>
        </div><br/><br/>
        <div class="reg_box">
            <div id="reg_con">
                <input id="submit" type="Submit" name="Submit" value="註冊"/>
            </div>
        </div>

    <script type="text/javascript">
        $('#submit').click(function(){
            if($('#password').val() != $('#confirm').val()){
                alert("密碼與確認密碼不符!");
                return false;
            }
            if($('.subject:checked').length == 0){
                alert("請選擇學科!");
                return false;
            }
            var subject = [];
            for(var i = 0; i < $('.subject:checked').length; i++){
                subject.push($('.subject:checked')[i].value);
            }
            $.ajax({
                url: '/admin/user_action.php',
                type: 'POST',
                data: {
                    type: 'addUser',
                    username: $('input[name=username]').val(),
                    password: $('input[name=password]').val(),
                    name: $('input[name=name]').val(),
                    username: $('input[name=username]').val(),
                    sex: $('select[name=sex] :selected').val(),
                    borndate: $('input[name=borndate]').val(),
                    grade: $('select[name=grade] option:selected').val(),
                    subject: subject,
                    telephone: $('input[name=telephone]').val(),
                    adress: $('input[name=adress]').val(),
                    email: $('input[name=email]').val(),
                    parents: $('input[name=parents]').val(),
                    level: $('select[name=level] :selected').val()
                }
            }).done(function(data){
                var result = JSON.parse(data);
                if(result.status == 'true'){
                    alert('用戶新增完成');
                    window.location.href = '/admin/user.php';
                }else{
                    if(result.message){
                        alert("錯誤: "+result.message);
                    }else{
                        alert("錯誤: 用戶未新增");
                    }
                }
            });
            return false;
        });
    </script>