module Tpc
  module Tags
    class RecentNews < Liquid::Tag
      def render(context)
        site = context.registers[:site]
        post = site.tags['news'].first
        "<a href='#{post.url}'>#{post['description'].gsub(/^(.{80,}?).*$/m,'\1...')}</a>"
      end
    end
  end
end

Liquid::Template.register_tag('recent_news', Tpc::Tags::RecentNews)
