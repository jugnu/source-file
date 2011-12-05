class SocialNetworksController < ApplicationController
  def face_book_connect
    redirect_to "https://graph.facebook.com/oauth/authorize?client_id=109494139138232&scope=email,offline_access,user_photos&redirect_uri=http://localhost:3000/social_networks/face_book_profile/&display=popup"
  end

  def face_book_profile

    app_id = '109494139138232'
    secret = '93008a12deeb24f96f40d3a35d9de238'
    redirect_uri = 'http://localhost:3000/social_networks/face_book_profile/'

    unless params["code"].blank?
      code = params["code"]

      oauth_url = "https://graph.facebook.com/oauth/access_token?client_id=#{app_id}&redirect_uri=#{URI.escape(redirect_uri)}&client_secret=#{secret}&code=#{URI.escape(code)}"
      resp = RestClient.get oauth_url

      params = {}
      params_array = resp.split("&")
      params_array.each do |p|
        ps = p.split("=")
        params[ps[0]] = ps[1]
      end
      access_token_hash = params
      @access_token = access_token_hash["access_token"]
      session[:key] = @access_token

      rg = RestGraph.new(:access_token => @access_token,
        :graph_server => 'https://graph.facebook.com/',
        :fql_server   => 'https://api.facebook.com/',
        :accept       => 'text/javascript',
        :lang         => 'en-us', # this affect search
        :auto_decode  => true,    # decode by json
        :app_id       => '192366697475051',
        :secret       => 'e2258ac1f6e0213c9d4e2d8584c7f65c')

      #    rg.post('me/feed', :message => 'Test post from my test app')
      @ab = rg.get('me')
      @login_user = rg.fql("SELECT pic_big FROM user WHERE uid = me()")
      Profile.create(:user_id => current_user.id) unless current_user.profile.blank?
      current_user.profile.update_attribute("face_book_image", @login_user[0]['pic_big'])
      flash[:notice] = "Facebook profile image uploaded successfully"
    else
      flash[:notice] = "Facebook profile image cannot be uploaded successfully"
    end
    redirect_to :controller => '/main_pages',:action => "index", :show => '1'
  end

  def all_friends
    @friends = []
    @friends_users = FriendsUsers.all
    @friends_users.each do |user|
      if user.status == 'approved'
        if user.user_id == current_user.id
          @friends << user
        elsif user.friend_id == current_user.id
          @friends << user
        end
      end
    end
    render :update do |page|
      page.replace_html 'popups', :partial => '/social_networks/all_friends'
    end
  end

  def friend_requests
    @friend_requests = []
    @friends_users = FriendsUsers.all
    @friends_users.each do |user|
      if user.status == 'Pending' and user.friend_id == current_user.id
         
        @friend_requests << user
        
      end
    end
    @friend_requests = @friend_requests.sort_by{|f| f.created_at}
    render :update do |page|
      page.replace_html 'popups', :partial => '/social_networks/friend_requests'
    end
    
  end


  def friend_feeds
    @friends = []
    @friends_users = FriendsUsers.all
    @friends_users.each do |user|
      if user.status == 'approved'
        if user.user_id == current_user.id
          @user = User.find_by_id(user.friend_id)
          @friends << @user
        elsif user.friend_id == current_user.id
          @user = User.find_by_id(user.user_id)
          @friends << @user
        end
      end
    end
    @feeds = []
    @friends.each do |friend|
      friend.moods.each do |m|
        @feeds << m
      end
    end
    @feeds = (@feeds.sort_by{|f| f.created_at}).reverse
    render :update do |page|
      page.replace_html 'popups', :partial => '/social_networks/feeds'
    end
  end
 
  def index
    render :update do |page|
      page.replace_html 'popups', :partial => '/social_networks/index'
    end
  end
  
end
