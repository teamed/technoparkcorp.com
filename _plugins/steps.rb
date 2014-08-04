module Tpc
  module Tags
    class Steps < Liquid::Tag
      def render(context)
        page = context['page']
        if page['next'].nil?
          nxt = 'process/scope'
        else
          nxt = page['next'].strip
        end
        li(find(nxt, context))
      end

      def find(name, context)
        site = context.registers[:site]
        site.posts.each do |post|
          if post.permalink == name
            return post
          end
        end
        raise "can't find article '#{name}'"
      end

      def li(post)
        "<li><a href='/#{post.permalink}'>#{post['intro']}</a></li>"
      end
    end
  end
end

Liquid::Template.register_tag('steps', Tpc::Tags::Steps)
